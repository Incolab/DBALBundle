<?php

namespace Incolab\DBALBundle\Service;

use Doctrine\DBAL\DriverManager;
use Symfony\Component\HttpKernel\Kernel;
use Psr\Log\LoggerInterface;
use Incolab\DBALBundle\Logger\QueryLogger;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class DBALService
 * @package Incolab\DBALBundle\Service
 */
class DBALService {

    /**
     * @var Stopwatch
     */
    protected $stopwatch;
    /**
     * @var Symfony\Component\HttpKernel\Kernel
     */
    protected $kernel;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var array The connection array
     */
    private $connections = array();
    /**
     * @var array The connection loggers array
     */
    private $loggers = array();
    /**
     * @var string|null The default connection
     */
    private $defaultConnection = null;
    /**
     * @var array The EntityRepositories array
     */
    private $managers = array();

    /**
     * DBALService constructor.
     * @param LoggerInterface $logger
     * @param Stopwatch $stopwatch
     * @param array $config
     */
    public function __construct(Kernel $kernel, LoggerInterface $logger = null,Stopwatch $stopwatch = null,$config = array()) {
        $this->kernel = $kernel;
        $this->stopwatch = $stopwatch;
        $this->logger = $logger;

        if(isset($config['connections'])) {
            foreach($config['connections'] as $name => $params) {
                if(is_null($this->defaultConnection)) {
                    $this->defaultConnection = $name;
                    $this->connections[$name] = $params;
                }
            }
        }

        if(isset($config['default_connection']) && !is_null($config['default_connection'])) {
            $this->defaultConnection = $config['default_connection'];
        }
    }

    /**
     * @return string The default connection
     */
    public function getDefaultConnection() {
        return $this->defaultConnection;
    }

    /**
     * @param string $defaultConnection
     * @return string The new default connection
     */
    public function setDefaultConnection($defaultConnection) {
        return $this->defaultConnection = $defaultConnection;
    }

    /**
     * @param string $connection_name
     * @return \Doctrine\DBAL\Connection
     * @throws \Doctrine\DBAL\DBALException
     * @throws \InvalidArgumentException
     */
    public function connect($connection_name = null) {
        if(is_null($connection_name)) {
            $connection_name = $this->getDefaultConnection();
        }

        if(!isset($this->connections[$connection_name])) {
            throw new \InvalidArgumentException("No such connection ".json_encode($connection_name).".");
        }

        $conn = DriverManager::getConnection($this->connections[$connection_name]);

        $conn->getConfiguration()->setSQLLogger($this->getLogger($connection_name));
        return $conn;
    }

    /**
     * @param string $connection_name
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getMigrationConfiguration($connection_name = null) {
        if(is_null($connection_name)) {
            $connection_name = $this->getDefaultConnection();
        }

        if(!isset($this->connections[$connection_name])) {
            throw new \InvalidArgumentException("No such connection ".json_encode($connection_name).".");
        }

        return $this->connections[$connection_name]['migrations'];
    }

    /**
     * @param string $connection_name
     * @return \Doctrine\DBAL\Query\QueryBuilder
     * @throws \Doctrine\DBAL\DBALException
     * @throws \InvalidArgumentException
     */
    public function query($connection_name = null) {
        $conn = $this->connect($connection_name);
        return $conn->createQueryBuilder();
    }

    /**
     * @param string $connection_name
     * @return \Doctrine\DBAL\Schema\AbstractSchemaManager
     * @throws \Doctrine\DBAL\DBALException
     * @throws \InvalidArgumentException
     */
    public function schemaManager($connection_name = null) {
        $conn = $this->connect($connection_name);
        return $conn->getSchemaManager();
    }

    /**
     * @internal used by the QueryCollector
     * @return array
     */
    public function getLoggers() {
        return $this->loggers;
    }

    private function getLogger($connection_name) {
        if(!isset($this->connections[$connection_name])) {
            throw new \InvalidArgumentException("No such connection ".json_encode($connection_name).".");
        }
        if(!isset($this->loggers[$connection_name])) {
            $this->loggers[$connection_name] = array();

        }
        return $this->loggers[$connection_name][] = new QueryLogger($connection_name,$this->logger,$this->stopwatch);
    }

    /**
     * @param string $entity_name
     * @return EntityRepository
     */
    public function getRepository($entity_name, $connection_name = null)
    {
        $key = $entity_name;
        if ($connection_name !== null) {
            $key = $key . $connection_name;
        }
        $hashedKey = hash("md5", $key);
        
        if (!isset($this->managers[$hashedKey])) {
            $bKeys = explode(":", $entity_name);
            $class = "\\" . $this->kernel->getBundle($bKeys[0])->getNamespace() . "\\Repository\\" .$bKeys[1] . "Repository";
            $this->managers[$hashedKey] = new $class($this->connect($connection_name));
        }
        return $this->managers[$hashedKey];
    }
    
}
