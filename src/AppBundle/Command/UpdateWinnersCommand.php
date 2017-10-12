<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 24.08.17
   * Time: 23:59
   */
  
  namespace AppBundle\Command;
  
  
  use AppBundle\Entity\City;
  use AppBundle\Entity\Lottery;
  use AppBundle\Entity\Rates;
  use AppBundle\Entity\Region;
  use Dalee\PEPUWSClientBundle\Controller\GeoApiController;
  use Dalee\PEPUWSClientBundle\Controller\PromoLotteryApiController;
  use Dalee\PEPUWSClientBundle\Exception\ApiFailedException;
  use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
  use Symfony\Component\Console\Input\InputInterface;
  use Symfony\Component\Console\Output\OutputInterface;
  use Symfony\Component\Security\Acl\Exception\Exception;
  
  class UpdateWinnersCommand extends ContainerAwareCommand
  {
    protected $defaultName;
    
    public function __construct($defaultName)
    {
      $this->defaultName = $defaultName;
      
      parent::__construct();
    }
    
    protected function configure()
    {
      $this
        ->setName('app:update-winners')
        ->setDescription('UpdateWinnersCommand.')
        ->setHelp('получение	справочника	Winners.');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
      // outputs multiple lines to the console (adding "\n" at the end of each line)
      $output->writeln([
        'UpdateWinnersCommand',
        '============',
        '',
      ]);
      
      $doctrine = $this->getContainer()->get('doctrine');
      $em = $doctrine->getManager();
      
      $api = new PromoLotteryApiController();
      $promos = $api->getPromos();
      foreach ($promos as $promo)
      {
        try
        {
          $output->writeln('get lotteryes for ' . $promo['slug']);
          $lotteries = $api->getLotteries($promo['slug']);
          foreach ($lotteries as $lottery)
          {
            $output->writeln(print_r($lottery, true));
          }
        }
        catch (ApiFailedException $e)
        {
          $output->writeln($e->getMessage());
        }
      }
      
      
      $em->flush();
      $output->writeln('Whoa!');
    }
  }