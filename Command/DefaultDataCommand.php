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
class DefaultDataCommand extends ContainerAwareCommand {

    protected $defaultData;

    protected function configure() {
        $this->setName("dbal:database:defaultdata")
                ->setDescription("Add default data")
                ->setHelp("This command allows you to add some default data")
                ->addArgument("repository", InputArgument::REQUIRED, "Repository name")
                ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $this->defaultData = $this->getContainer()->get("db.default_data");
        $output->writeln([
            '-----------------',
            'Default Data Creator',
            '=================',
            '',
        ]);

        $defaultDataManager = $input->getArgument("repository");
        $output->writeln(sprintf("Repository %s", $defaultDataManager));

        $manager = $this->defaultData->getDefaultDataManager($defaultDataManager);

        if (!method_exists($manager, "addDefaultData")) {
            $output->writeln(sprintf("'addDefaultData ' function don't exists inside '%s' DefaultData", $repositoryName));
            return null;
        }

        $manager->addDefaultData();
        
        $output->writeln(sprintf("DefaultData for : %s added", $defaultDataManager));
    }
}
