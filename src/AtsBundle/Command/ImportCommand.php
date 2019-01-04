<?php
namespace  AtsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('ats:cron:importRss');

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text = "Traitement en cours ...";
        $output->writeln($text);
        $url = $this->getContainer()->getParameter('url');
        if(!empty($url)){
            $result =$this->getContainer()->get('application_ats_service')->persistData($url);
            $i= $result['count'];
        }
        $output->writeln($i.' entry inserted ');
        $text = "Traitement termine";
        $output->writeln($text);
    }





//

}