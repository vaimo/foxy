<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
namespace Foxy\Strategy;

class BootstrapStrategy
{
    /**
     * @var \Foxy\Composer\Context
     */
    private $composerContext;

    /**
     * @param \Foxy\Composer\Context $composerContext
     */
    public function __construct(
        \Foxy\Composer\Context $composerContext
    ) {
        $this->composerContext = $composerContext;
    }

    public function shouldAllow()
    {
        $composer = $this->composerContext->getLocalComposer();

        $packageResolver = new \Foxy\Composer\Plugin\PackageResolver(
            array($composer->getPackage())
        );

        $repository = $composer->getRepositoryManager()->getLocalRepository();

        try {
            $packageResolver->resolveForNamespace($repository->getCanonicalPackages(), __NAMESPACE__);
        } catch (\Foxy\Exception\PackageResolverException $exception) {
            return false;
        }

        return true;
    }
}
