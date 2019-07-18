<?php

/*
 * This file is part of the Foxy package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Foxy;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\InstallerEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Composer\Installer\PackageEvents;
use Composer\Installer\PackageEvent;
use Composer\Util\Filesystem;
use Composer\Util\ProcessExecutor;
use Foxy\Asset\AssetManagerInterface;
use Foxy\Config\Config;
use Foxy\Config\ConfigBuilder;
use Foxy\Exception\RuntimeException;
use Foxy\Fallback\AssetFallback;
use Foxy\Fallback\ComposerFallback;
use Foxy\Solver\Solver;
use Foxy\Solver\SolverInterface;
use Foxy\Util\ComposerUtil;
use Foxy\Util\ConsoleUtil;

/**
 * Composer plugin.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class Foxy implements PluginInterface, EventSubscriberInterface
{
    const REQUIRED_COMPOSER_VERSION = '1.5.0';

    /**
     * @var \Foxy\Composer\OperationAnalyser
     */
    protected $operationAnalyser;
    
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var AssetManagerInterface
     */
    protected $assetManager;

    /**
     * @var AssetFallback
     */
    protected $assetFallback;

    /**
     * @var ComposerFallback
     */
    protected $composerFallback;

    /**
     * @var SolverInterface
     */
    protected $solver;

    /**
     * @var bool
     */
    protected $enabled = true;
    
    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * The list of the classes of asset managers.
     */
    private static $assetManagers = array(
        'Foxy\Asset\NpmManager',
        'Foxy\Asset\YarnManager',
    );

    /**
     * The default values of config.
     */
    private static $defaultConfig = array(
        'enabled' => true,
        'manager' => 'npm',
        'manager-version' => array(
            'npm' => '>=5.0.0',
            'yarn' => '>=1.0.0',
        ),
        'manager-bin' => null,
        'manager-options' => null,
        'manager-install-options' => null,
        'manager-update-options' => null,
        'manager-timeout' => null,
        'composer-asset-dir' => null,
        'run-asset-manager' => true,
        'fallback-asset' => true,
        'fallback-composer' => true,
        'enable-packages' => array(),
    );

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            InstallerEvents::PRE_DEPENDENCIES_SOLVING => array(
                array('init', 100),
            ),
            ScriptEvents::POST_INSTALL_CMD => array(
                array('solveAssets', 100),
            ),
            ScriptEvents::POST_UPDATE_CMD => array(
                array('solveAssets', 100),
            ),
            PackageEvents::PRE_PACKAGE_UNINSTALL => 'disableFeatures'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function activate(Composer $composer, IOInterface $io)
    {
//        ComposerUtil::validateVersion(static::REQUIRED_COMPOSER_VERSION, Composer::VERSION);

        $input = ConsoleUtil::getInput($io);
        $executor = new ProcessExecutor($io);
        $fs = new Filesystem($executor);

        $this->operationAnalyser = new \Foxy\Composer\OperationAnalyser();

        $this->config = ConfigBuilder::build($composer, self::$defaultConfig, $io);
        $this->assetManager = $this->getAssetManager($io, $this->config, $executor, $fs);
        $this->assetFallback = new AssetFallback($io, $this->config, $this->assetManager->getPackageName(), $fs);
        $this->composerFallback = new ComposerFallback($composer, $io, $this->config, $input, $fs);
        $this->solver = new Solver($this->assetManager, $this->config, $fs, $this->composerFallback);

        $this->assetManager->setFallback($this->assetFallback);
    }

    /**
     * Init the plugin.
     */
    public function init()
    {
        if (!$this->enabled) {
            return;
        }
        
        if (!$this->initialized) {
            $this->initialized = true;
            $this->assetFallback->save();
            $this->composerFallback->save();

            if ($this->config->get('enabled')) {
                $this->assetManager->validate();
            }
        }
    }

    /**
     * Set the solver.
     *
     * @param SolverInterface $solver The solver
     */
    public function setSolver(SolverInterface $solver)
    {
        $this->solver = $solver;
    }

    /**
     * Solve the assets.
     *
     * @param Event $event The composer script event
     */
    public function solveAssets(Event $event)
    {
        if (!$this->enabled) {
            return;
        }
        
        $this->solver->setUpdatable(false !== strpos($event->getName(), 'update'));
        $this->solver->solve($event->getComposer(), $event->getIO());
    }

    /**
     * Disable the features of the plugin
     */
    public function disableFeatures(PackageEvent $event)
    {
        if (!$this->operationAnalyser->isUninstallOperationForNamespace($event->getOperation(), __NAMESPACE__)) {
            return;
        }

        $this->enabled = false;
    }

    /**
     * Get the asset manager.
     *
     * @param IOInterface     $io       The IO
     * @param Config          $config   The config
     * @param ProcessExecutor $executor The process executor
     * @param Filesystem      $fs       The composer filesystem
     *
     * @throws RuntimeException When the asset manager is not found
     *
     * @return AssetManagerInterface
     */
    protected function getAssetManager(IOInterface $io, Config $config, ProcessExecutor $executor, Filesystem $fs)
    {
        $manager = $config->get('manager');

        foreach (self::$assetManagers as $class) {
            $am = new $class($io, $config, $executor, $fs);

            if ($am instanceof AssetManagerInterface && $manager === $am->getName()) {
                return $am;
            }
        }

        throw new RuntimeException(sprintf('The asset manager "%s" doesn\'t exist', $manager));
    }
}
