<?php

namespace Incolab\DBALBundle\DataCollector;


use Incolab\DBALBundle\Service\DBALService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

class QueryCollector extends DataCollector implements DataCollectorInterface {

    private $loggers;

    public function __construct(DBALService $service = null) {
        if(!is_null($service)) {
            $this->loggers = $service->getLoggers();
        }
    }

    public function collect(Request $request, Response $response, \Exception $exception = null) {
        $this->data = array(
            'connections' => array(),
            'totalQueries' => 0,
            'totalMS' => 0
        );
        if(!is_null($this->loggers)) {
            foreach($this->loggers as $name => $loggers) {
                $this->data['connections'][$name] = array(
                    "queries" => array(),
                    "totalQueries" => 0,
                    "totalMS" => 0
                );
                foreach($loggers as $logger) {
                    foreach($logger->queries as $query) {
                        $this->data['connections'][$name]['queries'][] = $query;
                        $this->data['connections'][$name]['totalQueries']++;
                        $this->data['connections'][$name]['totalMS'] += $query['executionMS'];
                    }
                }
                $this->data['totalQueries'] += $this->data['connections'][$name]['totalQueries'];
                $this->data['totalMS'] += $this->data['connections'][$name]['totalMS'];
            }
        }
    }

    public function getConnections() {
        return $this->data['connections'];
    }

    public function getQueryCount() {
        return $this->data['totalQueries'];
    }

    public function getExecutionTime() {
        return $this->data['totalMS'];
    }

    public function getName() {
        return 'db';
    }
}