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

use Composer\DependencyResolver\Operation\OperationInterface;

class OperationAnalyser
{
    /**
     * @var \Foxy\Composer\Package\ConfigAnalyser
     */
    private $configAnalyser;

    public function __construct()
    {
        $this->configAnalyser = new \Foxy\Composer\Package\ConfigAnalyser();
    }

    public function isUninstallOperationForNamespace(OperationInterface $operation, $namespace)
    {
        if (!$operation instanceof \Composer\DependencyResolver\Operation\UninstallOperation) {
            return false;
        }
        
        return $this->configAnalyser->ownsNamespace($operation->getPackage(), $namespace);
    }
}
