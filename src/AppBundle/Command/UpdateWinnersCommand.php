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
  use AppBundle\Entity\Region;
  use AppBundle\Entity\Winner;
  use AppBundle\Repository\LotteryRepository;
  use Dalee\PEPUWSClientBundle\Controller\GeoApiController;
  use Dalee\PEPUWSClientBundle\Controller\ParticipantApiController;
  use Dalee\PEPUWSClientBundle\Controller\PromocodeApiController;
  use Dalee\PEPUWSClientBundle\Controller\PromoLotteryApiController;
  use Dalee\PEPUWSClientBundle\Entity\PromocodeApplication;
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
      foreach (["moda_lamoda_weekly", "moda_yves_rocher_weekly", "moda_lenina_weekly"] as $promo_slug)
      {
        $output->writeln("Lotteries $promo_slug");
        $lotteries = $lotteryApi->getLotteries($promo_slug);
        
        foreach ($lotteries as $lottery)
        {
//          $output->writeln("Lottery $promo_slug " . $lottery['id']);
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
      
      foreach ($winners as $winner)
      {
        foreach ($winner['promocodes'] as $promocode)
        {
          //$w = $this->doctrine->getRepository('AppBundle:Winner')->find($promocode['id']);
          $w = $this->doctrine->getRepository('AppBundle:Winner')->findOneBy(['promocode_id' => $promocode['id'], 'lottery_id' => $lottery->getId()]);
          
          $output->writeln("promocode: {$promocode['id']}\tlottery:" . $lottery->getId() . "\t" . $lottery->getStartTime()->format("Y-m-d H:i:s") . "\t" . $lottery->getEndTime()->format("Y-m-d H:i:s"));
          if (!$w)
          {
            $w = new Winner();
          }
          $w->setPromocodeId($promocode['id']);
          $w->setLotteryId($lottery->getId());
          $w->setPrize($lottery->getPrize());
          $promocode_date = $this->getPromocodeDate($promocode['participant']['id'], $promocode['id']);
          $w->setWinDate($promocode_date);
          $w->setPromocodeParticipantId($promocode['participant']['id']);
          $w->setPromocodeParticipantCrmIdIlp($promocode['participant']['crm_id_ilp']);
          $w->setPromocodeParticipantGuid($promocode['participant']['guid']);
          try
          {
              $participant = $participantApi->getById($promocode['participant']['id'], ['firstname', 'lastname', 'email']);
              $w->setPromocodeParticipantFio($participant->lastname . " " . $participant->firstname);
              $w->setPromocodeParticipantEmail($participant->email);
              $this->em->merge($w);
              $this->em->flush();
          }catch (\Exception $e){
              continue;
          }
        }
      }
    }
    
    private function getPromocodeDate($participant_id, $promocode_id)
    {
      $promocode_applications = (new PromocodeApiController())->getApplicationsByParticipantId($participant_id);
      /**
       * @var $promocode_applications PromocodeApplication[]
       */
      foreach ($promocode_applications as $application)
      {
        if ($application->getId() == $promocode_id)
        {
          return new \DateTime($application->getValidationDate());
        }
      }
      
      return null;
    }
  }