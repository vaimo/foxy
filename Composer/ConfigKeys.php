<?php

/*
 * This file is part of the Foxy package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Foxy\Composer;

class ConfigKeys
{
    const PACKAGE_CONFIG_FILE = 'composer.json';

    const VENDOR_DIR = 'vendor-dir';
    const CONFIG_ROOT = 'extra';
    const PSR4_CONFIG = 'psr-4';
    const COMPOSER_PLUGIN_TYPE = 'composer-plugin';

    const PACKAGES = 'packages';
    const PACKAGES_DEV = 'packages-dev';
    const ALIASES = 'aliases';
    const PLATFORM_OVERRIDES = 'platform-overrides';

    const PLATFORM = 'platform';
    const PLATFORM_DEV = 'platform-dev';

    const MINIMUM_STABILITY = 'minimum-stability';
    const STABILITY_FLAGS = 'stability-flags';
    const PREFER_STABLE = 'prefer-stable';
    const PREFER_LOWEST = 'prefer-lowest';

    const ALIAS_PACKAGE = 'package';
    const ALIAS_VERSION = 'version';
}