<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
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
