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
  use AppBundle\Repository\LotteryRepository;
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
    /***
     * @var \Symfony\Bridge\Doctrine\RegistryInterface
     */
    private $doctrine;
    /***
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $em;
    
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
      
      $this->doctrine = $this->getContainer()->get('doctrine');
      $this->em = $this->doctrine->getManager();
      
      $lotteryApi = new PromoLotteryApiController();
      foreach (["moda_lamoda_weekly", "moda_yves_rocher_weekly", "moda_lenina_weekly", "moda_dream"] as $promo_slug)
      {
        $lotteries = $lotteryApi->getLotteries($promo_slug);
        foreach ($lotteries as $lottery)
        {
          $Lottery = $this->doctrine->getRepository('AppBundle:Lottery')->find($lottery['id']);
          if (!$Lottery)
          {
            $Lottery = new Lottery();
            $Lottery->setId($lottery['id']);
            $Lottery->setIsRunnable($lottery['is_runnable']);
            $Lottery->setIsReady($lottery['is_ready']);
            $Lottery->setIsDone($lottery['is_done']);
            $Lottery->setPrize($lottery['prize']['slug']);
            
            $date = \DateTime::createFromFormat("Y-m-d", date("Y-m-d", strtotime($lottery['prize']['balance_date'])));
            $Lottery->setBalanceDate($date);
            $Lottery->setStartTime(new \DateTime($lottery['start_time']));
            $Lottery->setEndTime(new \DateTime($lottery['end_time']));
            $Lottery->setLastUpdated(new \DateTime());
            $this->em->merge($Lottery);
            $this->em->flush();
          }
          
          if ($lottery['is_ready'])
          {
            $winners = $lotteryApi->getLotteryWinners($promo_slug, $lottery['id']);
            $this->updateWinners($winners, $Lottery, $output);
          }
        }
      }
      
      
      $output->writeln('Whoa!');
    }
    
    /**
     * @param                 $winners
     * @param Lottery         $lottery
     * @param OutputInterface $output
     */
    private function updateWinners($winners, $lottery, $output)
    {
      $participantApi = new ParticipantApiController();
      /**
       * {
       *   "tickets_count": null,
       *   "is_active": true,
       *   "is_winner": true,
       *   "prize_application": null,
       *   "id": 224,
       *   "promocodes": [
       *     {
       *       "id": 3149,
       *       "participant": {
       *         "id": 1111111,
       *         "crm_id_ilp": "1111111",
       *         "guid": "9a15a7dd-edaf-5cf6-b5b7-8e1adcaabd63",
       *         "crm_data": []
       *       }
       *     }
       *   ]
       * }
       */
      foreach ($winners as $winner)
      {
        $w = $this->doctrine->getRepository('AppBundle:Winner')->find($winner['id']);
        
        if (!$w)
        {
          $w = new Winner();
          $w->setId($winner['id']);
          $w->setPrizeApplication($winner['prize_application']);
          $w->setPrize($lottery->getPrize());
          $w->setWinDate($lottery->getBalanceDate());
          $w->setPromocodeParticipantId($winner['promocodes']['participant']['id']);
          $w->setPromocodeParticipantCrmIdIlp($winner['promocodes']['participant']['crm_id_ilp']);
          $w->setPromocodeParticipantGuid($winner['promocodes']['participant']['guid']);
          $participant = $participantApi->getById($winner['promocodes']['participant']['id'], ['firstname', 'lastname', 'email']);
          $w->setPromocodeParticipantFio($participant->lastname . " " . $participant->firstname);
          $w->setPromocodeParticipantEmail($participant->email);
          $this->em->merge($w);
          $this->em->flush();
        }
      }
    }
  }