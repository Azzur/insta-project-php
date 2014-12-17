<?php
/**
 * Created by PhpStorm.
 * User: Rodolphe
 * Date: 16/12/2014
 * Time: 15:10
 */

namespace Insta\PlanningBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SwitchUserTypeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('user:switch:type')
            ->setDescription('Switch user\'s type')
            ->addArgument('username', InputArgument::REQUIRED, 'The user\'s canonical username ')
            ->addArgument('type', InputArgument::REQUIRED, 'The user\'s new type')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $planningUser = $this->getContainer()->get('planning.users');

        $type = $input->getArgument('type');

        $em = $doctrine->getManager();
        $repository = $em->getRepository('PlanningBundle:User');
        $user = $repository->findOneByUsernameCanonical($input->getArgument('username'));

        if (is_null($user)) {
            $output->writeln('User not found.');
        } else {

            try {
                $planningUser->switchUserType($user, $type);
                $output->writeln('User modified.');
            } catch (\Exception $e) {
                $output->writeln($e->getMessage());
            }

        }


    }
}