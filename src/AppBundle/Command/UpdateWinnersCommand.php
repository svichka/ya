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
  use AppBundle\Entity\Winner;
  use Dalee\PEPUWSClientBundle\Controller\GeoApiController;
  use Dalee\PEPUWSClientBundle\Controller\ParticipantApiController;
  use Dalee\PEPUWSClientBundle\Controller\PromocodeApiController;
  use Dalee\PEPUWSClientBundle\Controller\PromoLotteryApiController;
  use Dalee\PEPUWSClientBundle\Exception\ApiFailedException;
  use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
  use Symfony\Component\Console\Input\InputInterface;
  use Symfony\Component\Console\Output\OutputInterface;
  use Symfony\Component\Debug\Exception\ContextErrorException;
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
      $lotteryApi = new PromoLotteryApiController();
      foreach (["moda_lamoda_weekly", "moda_yves_rocher_weekly", "moda_lenina_weekly", "moda_dream"] as $promo_slug)
      {
        $lotteries = $lotteryApi->getLotteries($promo_slug);
        foreach ($lotteries as $lottery)
        {
          if ($lottery['is_ready'])
          {
            $winners = $lotteryApi->getLotteryWinners($promo_slug, $lottery['id']);
            $this->updateWinners($winners, $lottery);
          }
        }
      }
      
      
      $output->writeln('Whoa!');
    }
  }