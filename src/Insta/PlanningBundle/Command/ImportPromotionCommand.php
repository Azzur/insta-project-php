<?php
/**
 * Created by PhpStorm.
 * User: Rodolphe
 * Date: 17/12/2014
 * Time: 09:21
 */

namespace Insta\PlanningBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Insta\PlanningBundle\Entity\Promotion;
use Insta\PlanningBundle\Entity\PromotionRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPromotionCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('planning:import:promotions')
            ->setDescription('Import promotions from CSV file')
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
        /** @var PromotionRepository $promotionRepository */
        $promotionRepository = $em->getRepository('PlanningBundle:Promotion');

        $separator  = $input->getOption('separator');
        $hasHeaders = $input->getOption('has-headers');

        $filename   = realpath($input->getArgument('filename'));
        $output->writeln('Loading file located at ' . $filename);

        $fp         = fopen($filename, 'r');

        /** @var array|Promotion[] $promotions */
        $promotions = array();
        $i = 0;
        while ($line = fgetcsv($fp, 512, $separator)) {
            if ($hasHeaders && $i == 0) {
                $i++;
                continue;
            }

            if ( !array_key_exists($line[7], $promotions) & !$promotionRepository->exists($line[7]) ) {

                $promotion = new Promotion();
                $promotion
                    ->setName((int) $line[7])
                    ->setDateStart(new \DateTime())
                    ->setDateEnd(new \DateTime())
                ;

                $promotions[$line[7]] = $promotion;

            }

            $i++;

        }

        $output->write('Add promotion : ');
        foreach ( $promotions as $name => $promotion ) {

            $em->persist($promotion);
            $output->write($promotion->getName() . ', ');
        }
        $em->flush();
        $output->writeln(' [TOTAL] ' .count($promotions) . ' promotions added.');

    }

}