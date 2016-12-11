<?php

/*
 * Copyright @ David Salbei
 */

namespace Incolab\DBALBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Incolab\DBALBundle\Service\DBALService;

/**
 * Description of DatabaseCreate
 *
 * @author David Salbei
 */
class DatabaseCreateCommand extends ContainerAwareCommand {

    protected $database;

    protected function configure() {
        $this->setName("dbal:database:create")
                ->setDescription("Create database")
                ->setHelp("This command allows you to create database")
                ->addArgument("repository", InputArgument::REQUIRED, "Repository name")
                ->addArgument("execute", InputArgument::OPTIONAL, "Execute Query")
                ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $this->database = $this->getContainer()->get("db");
        $output->writeln([
            '-----------------',
            'Database Creator',
            '=================',
            '',
        ]);

        $repositoryName = $input->getArgument("repository");
        $output->writeln(sprintf("Repository %s", $repositoryName));

        $repository = $this->database->getRepository($repositoryName);

        if (!method_exists($repository, "create_database")) {
            $output->writeln(sprintf("'create_database ' function don't exists inside '%s' repository", $repositoryName));
            return null;
        }

        $sqlRequests = $repository->create_database();

        $conn = $this->database->connect();

        if ($sqlRequests === null) {
            return;
        }

        foreach ($sqlRequests as $sql) {
            if ($input->hasArgument("execute") && $input->getArgument("execute") == "force") {
                $output->write("Execute ");
                $conn->query($sql);
            }
            $output->writeln(sprintf("Request: %s", $sql));
        }
    }

}
