<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Foxy\Factory;

class ComposerContextFactory
{
    /**
     * @var \Composer\Composer
     */
    private $composer;

    /**
     * @var \Composer\Composer
     */
    private static $globalComposer;

    public function __construct(
        \Composer\Composer $composer
    ) {
        $this->composer = $composer;
    }

    public function create()
    {
        $instances = array(
            $this->composer
        );

        if (!$this->isGlobalComposer($this->composer)) {
            if (self::$globalComposer === null) {
                self::$globalComposer = \Composer\Factory::createGlobal(new \Composer\IO\NullIO(), true);
            }

            array_unshift($instances, self::$globalComposer);
        }

        return new \Foxy\Composer\Context($instances);
    }

    private function isGlobalComposer()
    {
        $composerConfig = $this->composer->getConfig();

        $vendorDir = $composerConfig->get('vendor-dir');
        $homeDir = $composerConfig->get('home');

        return strpos($vendorDir, $homeDir) === 0;
    }
}
