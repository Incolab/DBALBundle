<?php

namespace Incolab\DBALBundle\Service;

use Symfony\Component\HttpKernel\Kernel;
use Incolab\DBALBundle\Service\DBALService;

/**
 * Class DefaultDataService
 * @package Incolab\DBALBundle\Service
 */
class DefaultDataService {
    
    /**
     * @var Symfony\Component\HttpKernel\Kernel
     */
    protected $kernel;
    
    protected $dbal;
    

    /**
     * 
     * @param Connection $dbal
     */
    public function __construct(Kernel $kernel, DBALService $dbal) {
        $this->kernel = $kernel;
        $this->dbal = $dbal;
    }

    /**
     * @param string $entity_name
     * @return EntityRepository
     */
    public function getDefaultDataManager($entity_name) {
        $bKeys = explode(":", $entity_name);
        $repository = $this->dbal->getRepository($entity_name);
        $class = "\\" . $this->kernel->getBundle($bKeys[0])->getNamespace() . "\\Resources\\SchemaDatabase\\DefaultData" . $bKeys[1];
        return new $class($repository, $this->kernel->getContainer());
    }

}
