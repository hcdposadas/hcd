<?php

namespace UtilBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use UtilBundle\Services\NotificationsManager;

class HcdNotifyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('hcd:notify')
            ->setDescription('...')
            ->addArgument('text', InputArgument::REQUIRED, 'Texto de la notificación')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('notifications.manager')->notify([
            'text' => $input->getArgument('text')
        ]);
    }
}
