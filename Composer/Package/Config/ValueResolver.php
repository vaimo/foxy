<?php

/*
 * This file is part of the Foxy package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Foxy\Composer\Package\Config;

use Foxy\Composer\ConfigKeys;

class ValueResolver
{
    public function resolveNamespaces(\Composer\Package\PackageInterface $package)
    {
        $autoload = $package->getAutoload();

        if (!isset($autoload[ConfigKeys::PSR4_CONFIG])) {
            return array();
        }

        return array_keys($autoload[ConfigKeys::PSR4_CONFIG]);
    } 
}
