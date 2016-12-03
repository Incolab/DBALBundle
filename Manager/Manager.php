<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Manager
 *
 * @author david
 */

namespace Incolab\DBALBundle\Manager;

use Doctrine\DBAL\Connection;


abstract class Manager {

    protected $dbal;

    public function __construct(Connection $dbal) {
        $this->dbal = $dbal;
    }

}
