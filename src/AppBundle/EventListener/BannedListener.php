<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 15.08.17
   * Time: 19:26
   */
  
  namespace AppBundle\EventListener;
  
  use AppBundle\Controller\PersonalController;
  use AppBundle\Controller\StaticController;
  use AppBundle\Form\Type\Participant\ParticipantFieldsFormType;
  use AppBundle\Form\Type\Participant\RegistrationFormType;
  use Dalee\PEPUWSClientBundle\Controller\GeoApiController;
  use Dalee\PEPUWSClientBundle\Controller\ParticipantApiController;
  use Dalee\PEPUWSClientBundle\Controller\PromoLotteryApiController;
  use Dalee\PEPUWSClientBundle\Entity\GeoCity;
  use Dalee\PEPUWSClientBundle\Entity\GeoRegion;
  use Dalee\PEPUWSClientBundle\Entity\Participant;
  use Dalee\PEPUWSClientBundle\Exception\ApiFailedException;
  use DateTime;
  use DateTimeZone;
  use Doctrine\Bundle\DoctrineBundle\Registry;
  use Psr\Log\LoggerInterface;
  use AppBundle\Controller\DefaultController;
  use Symfony\Component\HttpFoundation\JsonResponse;
  use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
  use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
  use Symfony\Component\Security\Acl\Exception\Exception;
  
  class BannedListener
  {
    private $end;
    private $start;
    private $doctrine;
    private $logger = null;
    private $action = null;
    private $c2 = null;
    private $cc = null;
    
    public function __construct(LoggerInterface $logger, $doctrine)
    {
      $this->doctrine = $doctrine;
      $this->end = "2018-12-31 23:59:59";
      $this->start = "2018-01-01 00:00:00";
      $this->logger = $logger;
      $this->logger->info('Inited BannedListener');
      $this->logger->info('Test log!!!!');
    }
    
    public function onKernelController(FilterControllerEvent $event)
    {
      $controller = $event->getController();
      
      if (!is_array($controller))
      {
        return;
      }
      
      $em = $this->getDoctrine()->getManager();
      ParticipantFieldsFormType::$em = $em;
      
      if ($controller[0] instanceof DefaultController || $controller[0] instanceof StaticController || $controller[0] instanceof PersonalController)
      {
        $this->logger->info("onKernelController");
        if ($this->action == null)
        {
          try
          {
            if (!$controller[0]->get('security.context')->isGranted('ROLE_USER'))
            {
              $this->action = '#authModal';
              
              return;
            }
            else
            {
              if ((new \DateTime())->format("Y-m-d H:i:s") > $this->end)
              {
                $this->action = '#eolModal';
                
                return;
              }
              if ((new \DateTime())->format("Y-m-d H:i:s") < $this->start)
              {
                $this->action = '#earlyModal';
                
                return;
              }
              $speedLock = $this->getDoctrine()->getRepository('AppBundle:SpeedLock')->findOneBy(['user' => $controller[0]->getUser()->getParticipant()->id]);
              if ($speedLock != null)
              {
                $date = new \DateTime();
                
                if ($speedLock->getTill() > $date)
                {
                  $this->action = "#speed1Modal";
                  
                  return;
                }
              }
              
              $api = new ParticipantApiController();
              try
              {
                $data = $api->getBanStatusById($controller[0]->getUser()->getParticipant()->id);
              }
              catch (ApiFailedException $e)
              {
                $data['status'] = 'bad';
              }
              
              if ($data['status'] == 'ok')
              {
                if (!$this->checkParticipantRequiredFields($controller[0]->getUser()->getParticipant()))
                {
                  $this->action = '#updateModal';
                }
//                elseif (!$this->checkParticipantAgreement($controller[0]->getUser()->getParticipant()))
//                {
//                  $this->action = '#agreeModal';
//                }
                elseif (!$this->checkParticipantAge($controller[0]->getUser()->getParticipant()))
                {
                  $this->action = '#ageModal';
                }
                else
                {
                  $this->action = '#uploadModal';
                }
              }
              else
              {
                switch ($data["reason"])
                {
                  case "TOO_MANY_INVALID_PROMOCODES":
                  case "TOO_MANY_INVALID_RECEIPTS":
                    $this->action = "#overflowModal";
                    
                    return;
                  case "TOO_MANY_PROMOCODES_PER_MINUTE":
                  case "TOO_MANY_RECEIPTS_PER_MINUTE":
                    $this->action = "#speedModal";
                    
                    return;
                }
                $this->action = '#banModal';
                
                return;
              }
            }
          }
          catch (Exception $e)
          {
            $this->action = '#authModal';
          }
        }
      }
    }
    
    public function checkParticipantRequiredFields(Participant $participant)
    {
      
      $this->logger->info("checkParticipantRequiredFields");
      $this->logger->info("checkParticipantRequiredFields " . $participant->id . " begin test ");
      $fields = [
        "ismale",
        "birthdate",
        "email",
        "firstname",
        "lastname",
        "cityguid",
        "regionguid",
      ];
      
      foreach ($fields as $field)
      {
        $this->logger->info("checkParticipantRequiredFields " . $field . " " . print_r($participant->{$field}, true));
        if ($participant->{$field} == '')
        {
          $this->logger->info("checkParticipantRequiredFields FAIL" . $participant->id . "  " . $field);
          
          return false;
        }
        else
        {
          $this->logger->info("checkParticipantRequiredFields GOOD" . $participant->id . "  " . $field);
        }
      }
      
      if ($participant->cityguid != '')
      {
        $city = $this->getDoctrine()->getRepository('AppBundle:City')->find($participant->cityguid);
        if ($city != null)
        {
          $participant->city = $city->getName();
        }
      }
      else
      {
        $city = $participant->city;
      }
      if ($participant->regionguid != '')
      {
        $region = $this->getDoctrine()->getRepository('AppBundle:Region')->find($participant->regionguid);
        if ($region != null)
        {
          $participant->region = $region->getName();
        }
      }
      else
      {
        $region = $participant->region;
      }
      
      if ($city == null || $region == null)
      {
        $this->logger->info("checkParticipantRequiredFields FAIL city or region");
        $this->logger->info("cityguid " . $participant->cityguid . " or regionguid " . $participant->regionguid);
        $this->logger->info("city " . $participant->city . " or region " . $participant->region);
        
        return false;
      }
      
      $this->logger->info("checkParticipantRequiredFields " . $participant->id . " test CORRECT");
      
      return true;
    }
    
    public function checkParticipantAge(Participant $participant)
    {
      $date = $participant->birthdate;
      $tz = new DateTimeZone('Europe/Moscow');
      $date = DateTime::createFromFormat('d.m.Y', $date, $tz);
      if ($date === false)
      {
        return false;
      }
      $age = $date
        ->diff(new DateTime('now', $tz))
        ->y;
      
      return $age >= 18;
    }
    
    /**
     * @param $participant Participant
     *
     * @return bool
     */
    public function checkParticipantAgreement($participant)
    {
      $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($participant->id);
      if (!$user)
      {
        return false;
      }
      if ($user->getAgree() !== 1)
      {
        return false;
      }
      
      return true;
    }
    
    public function getAction()
    {
      return $this->action;
    }
    
    public function isSended($guid)
    {
      $receipt = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findOneByGuid($guid);
      if ($receipt == null)
      {
        return false;
      }
      
      return $receipt->getSended() == 1;
    }
    
    public function isSms($id)
    {
      $u = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
      if ($u == null)
      {
        return false;
      }
      
      return $u->getMobileActivated() == 1;
    }
    
    public function getAssetsVersion()
    {
      return 16;
    }
    
    public function isFilled($id)
    {
      $u = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
      if ($u == null)
      {
        return false;
      }
      
      return $u->getMobileFilled() == 1;
    }
    
    public function regions()
    {
      $regions = $this->getDoctrine()->getRepository('AppBundle:Region')->findAll();
      $choices = [];
      foreach ($regions as $region)
      {
        $title = $region->getName();
        if ($region->getShortname())
        {
          $title = $region->getShortName() . ' ' . $title;
        }
        $choices[$title] = $region->getGuid();
        
      }
      
      return $choices;
    }
    
    public function cities($region_guid)
    {
      $cities = $this->getDoctrine()->getRepository('AppBundle:City')->findByRegion($region_guid);
      $choices = [];
      foreach ($cities as $city)
      {
        $title = $city['name'];
        if ($city['short_name'])
        {
          $title = $city['short_name'] . ' ' . $title;
        }
        $choices[$title] = $city['guid'];
        
      }
      
      return $choices;
    }
    
    public function getRegion($guid)
    {
      if ($guid == null)
      {
        return 'Регион не задан';
      }
      $region = $this->getDoctrine()->getRepository('AppBundle:Region')->find($guid);
      
      return $region != null ? $region->getName() : 'Регион не задан';
    }
    
    public function getCity($guid)
    {
      if ($guid == null)
      {
        return 'Регион не задан';
      }
      $city = $this->getDoctrine()->getRepository('AppBundle:City')->find($guid);
      
      return $city != null ? $city->getName() : 'Город не задан';
    }
    
    /**
     * @return Registry
     */
    public function getDoctrine()
    {
      return $this->doctrine;
    }
    
    public function plural($i)
    {
      return $this->splur($i, 'Долька', 'Дольки', 'Долек');
    }
    
    public function splur($n, $t1, $t2, $t3)
    {
      settype($n, 'string');
      $e1 = substr($n, -2);
      if ($e1 > 10 && $e1 < 20)
      {
        return $t3;
      } // "Teen" forms
      $e2 = substr($n, -1);
      switch ($e2)
      {
        case '1':
          return $t1;
          break;
        case '2':
        case '3':
        case '4':
          return $t2;
          break;
      }
      
      return $t3;
    }
    
    public function getLotteryDate()
    {
      $lotteries = $this->getDoctrine()->getRepository('AppBundle:Lottery')->findBy(['prize' => 'certificate_lamoda']);
      $weeks = [];
      $i = 1;
      foreach ($lotteries as $lottery)
      {
        if ($lottery->getStartTime() <= new \DateTime() && new \DateTime() <= $lottery->getEndTime())
        {
          $date = $lottery->getEndTime();
          $date->modify("+15 hours");
          
          return $date->format('d.m.Y');
        }
      }
      
      return "-";
    }
  }
