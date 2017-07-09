<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DefaultDataManager
 *
 * @author david
 */

namespace Incolab\DBALBundle\Manager;

use Incolab\DBALBundle\Manager\Manager;
use Symfony\Component\DependencyInjection\ContainerInterface;


abstract class DefaultDataManager {

    protected $manager;
    protected $container;

    public function __construct(Manager $manager, ContainerInterface $cont) {
        $this->manager = $manager;
        $this->container = $cont;
    }

}
