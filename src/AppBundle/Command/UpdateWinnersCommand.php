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
      
      $doctrine = $this->getContainer()->get('doctrine');
      $em = $doctrine->getManager();
      
      // 50 Рублей на телефон
      $users = $doctrine->getRepository('AppBundle:User')->findAll();
      foreach ($users as $user)
      {
        $output->writeln(['user = ' . $user->getId()]);
        /**
         * @var \Dalee\PEPUWSClientBundle\Entity\PromocodeApplication[] $promocodes
         */
        $output->writeln(['promocodes = ' . '-']);
        try
        {
          $promocodes = (new PromocodeApiController())->getApplicationsByParticipantId($user->getId());
          
          foreach ($promocodes as $application)
          {
            $output->writeln(['application = ' . $application->getId()]);
            $output->writeln(['application = ' . print_r($application,true)]);
            $promoApplications = $application->getPromoApplications();
            
            foreach ($promoApplications as $promoApplication)
            {
              
              if (count($promoApplication['prize_options']))
              {
                $output->writeln(['promoApplication = ' . print_r($promoApplication['prize_options'], true)]);
                $options = $promoApplication['prize_options'][0];
                $guid = $application->getCode();
                $win_receipts[$guid] = $options['slug'];
                
                $output->writeln(['win', $application->getId()]);
                $win_object = new Winner();
                $win_object->setPromocodeParticipantPrize(1);
                $win_object->setPromocodeParticipantDate(date('d.m.Y', strtotime($application->getReceipt()['registration_time'])));
                $win_object->setId($application->getId());
                $win_object->setPromocodeId($options['id']);
                $win_object->setPromocodeParticipantId($user->getId());
                
                $pApi = new ParticipantApiController();
                $output->writeln(['get User = ' . $user->getId()]);
                /**
                 * @var \Dalee\PEPUWSClientBundle\Entity\Participant $p
                 */
                $p = $pApi->getById($user->getId(), ['firstname', 'secname', 'lastname']);
                $fio = "";
                $fio .= $p->getLastname() . " ";
                $fio .= $p->getFirstname() . " ";
                $fio .= $p->getSecname() . " ";
                $fio = trim($fio);
                $win_object->setPromocodeParticipantFio($fio);
                if ($em->contains($win_object))
                {
                  $em->merge($win_object);
                }
                else
                {
                  $em->persist($win_object);
                }
                $output->writeln(['win flush']);
                $em->flush();
              }
            }
          }
        }
        catch (ApiFailedException $e)
        {
          $output->writeln([$e->getMessage()]);
        }
      }
      
      $output->writeln(['win lotteries']);
      $api = new PromoLotteryApiController();
      $promos = $api->getPromos();
      foreach ($promos as $promo)
      {
        try
        {
          $output->writeln('get lotteryes for ' . $promo['slug']);
          try
          {
            $lotteries = $api->getLotteries($promo['slug']);
            foreach ($lotteries as $lottery)
            {
              $winners = $api->getLotteryWinners($promo['slug'], $lottery['id']);
              foreach ($winners as $winner)
              {
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
                 * },
                 */
                $promocodes = $winner['promocodes'];
                foreach ($promocodes as $promocode)
                {
                  $win_object = new Winner();
                  
                  $win_object->setTicketsCount($winner['tickets_count']);
                  $win_object->setIsActive($winner['is_active']);
                  $win_object->setIsWinner($winner['is_winner']);
                  $win_object->setPrizeApplication($winner['prize_application']);
                  $win_object->setId($winner['id']);
                  $win_object->setPromocodeId($lottery['prize']['id']);
                  $win_object->setPromocodeParticipantId($promocode['participant']['id']);
                  $win_object->setPromocodeParticipantCrmIdIlp($promocode['participant']['crm_id_ilp']);
                  $win_object->setPromocodeParticipantGuid($promocode['participant']['guid']);
                  $win_object->setPromocodeParticipantCrmData(serialize($promocode['participant']['crm_data']));
// Для списка
                  $win_object->setPromocodeParticipantDate($lottery['prize']['balance_date']);
                  $win_object->setPromocodeParticipantPrize($lottery['prize']['ya_certificate_metro']);
                  
                  $pApi = new ParticipantApiController();
                  $p = $pApi->getById($promocode['participant']['id'], ['firstname', 'secname', 'lastname']);
                  $fio = "";
                  $fio .= $p->lastname . " ";
                  $fio .= $p->firstname . " ";
                  $fio .= $p->secname . " ";
                  $fio = trim($fio);
                  $win_object->setPromocodeParticipantFio($fio);
                  $em->merge($win_object);
                  $em->flush();
                }
              }
              $output->writeln(print_r($lottery, true));
            }
          }
          catch (ContextErrorException $e)
          {
            $output->writeln("Error getLotteries: " . $e->getMessage());
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