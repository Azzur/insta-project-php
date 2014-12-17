<?php
/**
 * Created by PhpStorm.
 * User: Rodolphe
 * Date: 17/12/2014
 * Time: 09:22
 */

namespace Insta\PlanningBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use Insta\PlanningBundle\Entity\Promotion;
use Insta\PlanningBundle\Entity\PromotionRepository;
use Insta\PlanningBundle\Entity\Student;
use Insta\PlanningBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportStudentCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('planning:import:students')
            ->setDescription('Import students from CSV file')
            ->addArgument('filename', InputArgument::REQUIRED, 'The path to the file.')
            ->addOption('has-headers', null, InputOption::VALUE_NONE, 'Set it if the CSV has headers line.')
            ->addOption('separator', null, InputOption::VALUE_REQUIRED, 'Set the separator character. [\';\']', ';')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Registry $doctrine */
        $doctrine   = $this->getContainer()->get('doctrine');
        /** @var EntityManager $em */
        $em         = $doctrine->getManager();
        /** @var UserManager $um */
        $um         = $this->getContainer()->get('fos_user.user_manager');
        /** @var PromotionRepository $promotionRepository */
        $promotionRepository = $em->getRepository('PlanningBundle:Promotion');

        $separator  = $input->getOption('separator');
        $hasHeaders = $input->getOption('has-headers');

        $filename   = realpath($input->getArgument('filename'));
        $output->writeln('Loading file located at ' . $filename);

        $fp         = fopen($filename, 'r');

        /** @var array|User[] $users */
        $users = array();
        $i = 0;
        while ($line = fgetcsv($fp, 512, $separator)) {
            if ($hasHeaders && $i == 0) {
                $i++;
                continue;
            }

            if ( !array_key_exists($line[0], $users) & !is_null($promotion = $promotionRepository->loadByName($line[7])) ) {

                $user = new Student();
                $user
                    ->setUsername($line[0])
                    ->setFirstname(($line[2]))
                    ->setLastname(($line[3]))
                    ->setEmail($line[1])
                    ->setPlainPassword( 'insta' . (new \DateTime())->format('Y') )
                    ->setPromotion($promotion)
                ;

                $users[ $user->getUsername() ] = $user;

            }

            $i++;

        }
        $errors = array();
        $duplicates = array();
        $output->write('Add user : ');
        foreach ( $users as $name => $user ) {
            try {
                $um->updateUser($user);
                $output->write($user->getFullname() . ', ');
            } catch (UniqueConstraintViolationException $e) {
                $duplicates[] = $user;
                $doctrine->resetManager();
            } catch (\Exception $e) {
                $errors[] = $user;
                $doctrine->resetManager();
            }
        }

        $output->writeln(' [TOTAL] ' .count($users) . ' users added.');
        $output->writeln('[DUPLICATES] ' .count($duplicates) . ' users not inserted, already exist.');
        $output->writeln('[ERRORS] ' .count($errors) . ' users not inserted, errors.');

    }

}