<?php

/*
 * This file is part of the Foxy package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Foxy\Composer\Package;

class ConfigAnalyser
{
    /**
     * @var \Foxy\Composer\Package\Config\ValueResolver
     */
    private $configValueResolver;

    public function __construct()
    {
        $this->configValueResolver = new \Foxy\Composer\Package\Config\ValueResolver();
    }

    public function isPluginPackage(\Composer\Package\PackageInterface $package)
    {
        return $package->getType() === \Foxy\Composer\ConfigKeys::COMPOSER_PLUGIN_TYPE;
    }

    public function ownsNamespace(\Composer\Package\PackageInterface $package, $namespace)
    {
        return (bool)array_filter(
            $this->configValueResolver->resolveNamespaces($package),
            function ($item) use ($namespace) {
                return strpos($namespace, rtrim($item, '\\')) === 0;
            }
        );
    }
}
