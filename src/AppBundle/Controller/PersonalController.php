<?php
  
  namespace AppBundle\Controller;
  
  use AppBundle\Entity\User;
  use Dalee\PEPUWSClientBundle\Controller\CrmReceiptsController;
  use Dalee\PEPUWSClientBundle\Controller\GeoApiController;
  use Dalee\PEPUWSClientBundle\Controller\LedgerApiController;
  use Dalee\PEPUWSClientBundle\Controller\PrizeApiController;
  use Dalee\PEPUWSClientBundle\Controller\PromocodeApiController;
  use Dalee\PEPUWSClientBundle\Controller\PromoLotteryApiController;
  use Dalee\PEPUWSClientBundle\Controller\ReceiptApiController;
  use Dalee\PEPUWSClientBundle\Exception\ApiFailedException;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\JsonResponse;
  use Symfony\Component\HttpFoundation\Request;
  
  use Dalee\PEPUWSClientBundle\Entity\Participant;
  use Dalee\PEPUWSClientBundle\Controller\ParticipantApiController;
  use Dalee\PEPUWSClientBundle\Exception\NotCorrectDataException;
  use Twig_SimpleFilter;
  
  
  class PersonalController extends Base
  {
    private $messages = [];
    private $errors = [];
    private $valid;

//    /**
//     * @Route("/perdbg", name="per_dbg_page")
//     */
//    public function dbgAction(Request $request)
//    {
//      $user = $this->getUser();
//      if (!$this->get('security.context')->isGranted('ROLE_USER'))
//      {
//        return $this->redirectToRoute('index_page', ['show' => 'auth']);
//      }
//      $participant = $user->getParticipant();
//      $list = $participant->getFieldList();
//      $p = [];
//      foreach ($list as $item)
//      {
//        $p[$item] = $participant->{$item};
//      }
//
//      return new JsonResponse([
//        "status"      => 200,
//        'participant' => $p,
//      ]);
//    }
    
    /**
     * @Route("/agree", name="agree_page")
     */
    public function agreeAction(Request $request)
    {
      $user = $this->getUser();
      if (!$this->get('security.context')->isGranted('ROLE_USER'))
      {
        return $this->redirectToRoute('login', ['reason' => 'access_denied']);
      }
      $participant = $user->getParticipant();
      
      $agreement = $request->request->get('agreement', 0);
      if ($agreement == 'Согласен')
      {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($participant->id);
        if ($user)
        {
          $user->setAgree(1);
          $this->getDoctrine()->getManager()->merge($user);
          $this->getDoctrine()->getManager()->flush();
        }
        else
        {
          $user = new User();
          $user->setId($participant->id);
          $user->setPreMobileStatus($participant->getIsphoneactivated());
          $user->setAgree(1);
          $this->getDoctrine()->getManager()->merge($user);
          $this->getDoctrine()->getManager()->flush();
        }
        
        return $this->redirectToRoute('personal_page');
      }
      else
      {
        return $this->redirectToRoute('index_page');
      }
    }
    
    /**
     * @Route("/personal/", name="personal_page")
     */
    public function personalAction(Request $request)
    {
      $this->get('logger')->error('log test personalAction');
      
      $user = $this->getUser();
      if (!$this->get('security.context')->isGranted('ROLE_USER'))
      {
        return $this->redirectToRoute('login', ['show' => 'auth']);
      }
      $participant = $user->getParticipant();
      
      $this->get('logger')->info('passed checkParticipantRequiredFields');
      
      // TODO: Включить проверку на локальное согласие
//      if (!$this->get('app.users.banned_listener')->checkParticipantAgreement($participant))
//      {
//        return $this->redirectToRoute('index_page', ['show' => 'agree']);
//      }
      
      if (!$this->get('app.users.banned_listener')->checkParticipantAge($participant))
      {
        return $this->redirectToRoute('index_page', ['show' => 'age']);
      }
      
      if ($participant->id == 24934)
      {
        if (($idDalee = $request->query->get('idDalee', -1)) != -1)
        {
          $fields = ['lastname', 'firstname', 'secname', 'region', 'city', 'regionguid', 'cityguid', 'birthdate', 'email', 'ismale'];
          $participant = (new ParticipantApiController())->getById($idDalee, $fields);
          $user->setParticipant($participant);
          $this->get('logger')->info('----------------------------');
          $this->get('logger')->info(print_r($participant, true));
          $this->get('logger')->info('----------------------------');
        }
      }
      
      $this->get('logger')->info("USER DATA LINK " . $participant->id . " " . $participant->email);
      $promocodes_info = $this->getPromocodes($user->getParticipant()->id);
      
      return $this->render('AppBundle:Default:personal.html.twig', [
        'messages'    => $this->messages,
        'errors'      => $this->errors,
        'weeks'       => $promocodes_info['weeks'],
        'promocodes'  => $promocodes_info['promocodes'],
        'participant' => $participant,
      ]);
    }
    
    private function getPromocodes($participant_id)
    {
      // Массив промокодов по неделям
      $all_promocodes = [];
      // Массив недель
      $tmp_weeks = [];
      $weeks = $this->getWeeks();
      
      /**
       * @var \Dalee\PEPUWSClientBundle\Entity\PromocodeApplication[] $promocode_applications
       */
      $promocode_applications = (new PromocodeApiController())->getApplicationsByParticipantId($participant_id);
      
      foreach ($weeks as $key => $week)
      {
        $i = $key + 1;
        $tmp_weeks["Неделя " . $i] = $week;
        if (!isset($all_promocodes["Неделя " . $i]))
        {
          $all_promocodes["Неделя " . $i] = [];
        }
        
        $start = $week['start']->format("Y-m-d H:i:s");
        $end = $week['end']->format("Y-m-d H:i:s");
        
        foreach ($promocode_applications as $application)
        {
          $c_date = date("Y-m-d H:i:s", strtotime($application->getValidationDate()));
          
          if ($start <= $c_date && $c_date <= $end)
          {
            if (!isset($all_promocodes[$i]))
            {
              $all_promocodes[$i] = [];
            }
            
            /***
             * @var $prizeApplications \Dalee\PEPUWSClientBundle\Entity\PrizeApplication[]
             */
            $prizeApplications = $application->getPrizeApplications();
            $promoApplications = $application->getPromoApplications();
            
            $validation_status = "Не известен";
            switch ($application->getValidationStatus())
            {
              case "VALID":
                $validation_status = "Принят";
                break;
            }
            $all_promocodes["Неделя " . $i][] = [
              'code'   => $application->getCode(),
              'date'   => $application->getValidationDate(),
              'status' => $validation_status,
              'prizes' => $prizeApplications,
              'promos' => $promoApplications,
            ];
          }
        }
      }
      
      return ['weeks' => $tmp_weeks, 'promocodes' => $all_promocodes];
    }
    
    private function makeErrorsFromFields($fields)
    {
      foreach ($fields as $field => $status)
      {
        $message = '';
        $message .= $this->get('translator')->trans('Not correct status ' . $status, [], 'personal');
        $message .= ' ';
        $field = strtolower($field);
        $field = str_replace('_', ' ', $field);
        $field = strtoupper(substr($field, 0, 1)) . substr($field, 1);
        $message .= $this->get('translator')->trans($field, [], 'personal');
        $this->errors[] = $message;
      }
    }
    
    
    public function validate($val, $err)
    {
      if (trim($val) == "")
      {
        $this->errors[] = $err;
        $this->valid = false;
      }
    }
    
    /**
     * @Route("/registration_u_json/", name="registration_u_json_page")
     */
    public
    function registrationUJsonAction(Request $request)
    {
      $participantApi = new ParticipantApiController();
      $request->getClientIp();
      
      $formData = $this->getUser()->getParticipant();
      $registration_form = $request->request->get('registration_form');
      $this->valid = true;
      
      $this->get('logger')->info('$registration_form ' . print_r($registration_form, true));
      
      if ($formData->firstname == '')
      {
        $formData->firstname = $registration_form['firstname'];
        $this->validate($formData->firstname, "Введите имя");
      }
      if ($formData->lastname == '')
      {
        $formData->lastname = $registration_form['lastname'];
        $this->validate($formData->lastname, "Введите фамилию");
      }
      if (isset($registration_form['secname']))
      {
        $formData->secname = $registration_form['secname'];
      }
      
      if ($formData->birthdate == '')
      {
        $formData->birthdate = $registration_form['birthdate'];
        $this->validate($formData->birthdate, "Введите дату рождения");
      }
      
      $formData->countrycode = 'RU';
      if ($formData->regionguid == '')
      {
        $formData->regionguid = $registration_form['regionguid'];
        $this->validate($formData->region, "Выберите регион");
      }
      if ($formData->city == '')
      {
        $formData->cityguid = $registration_form['cityguid'];
        $this->validate($formData->city, "Выберите город");
      }
      if ($formData->ismale == '')
      {
        $formData->ismale = $registration_form['ismale'];
        $this->validate($formData->ismale, "Выберите пол");
      }
      
      if ($this->valid)
      {
        try
        {
          if ($registration_form == null)
          {
            throw new NotCorrectDataException("Форма не заполена");
          }
          $p = new Participant();
          foreach (array_keys($registration_form) as $array_key)
          {
            if (in_array($array_key, ['agreement', 'iz18', 'password', 'password_confirm'])) // , 'region', 'city'
            {
              continue;
            }
            $p->{$array_key} = $registration_form[$array_key];
          }
          $p2 = $participantApi->update($formData->id, $p);
          $fields = ['lastname', 'firstname', 'secname', 'region', 'city', 'regionguid', 'cityguid', 'birthdate', 'ismale'];
          $p2 = $participantApi->getById($formData->id, $fields);
          
          $this->getUser()->setParticipant($p2);
          $participant = $this->getUser()->getParticipant();
          $participant->setCityguid($p2->getCityguid());
          $participant->setRegionguid($p2->getRegionguid());
          
          // Пишем локальное согласие с правилами
          $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($participant->id);
          if ($user)
          {
            $user->setAgree(1);
            $this->getDoctrine()->getManager()->merge($user);
            $this->getDoctrine()->getManager()->flush();
          }
          else
          {
            $user = new User();
            $user->setId($participant->id);
            $user->setAgree(1);
            $this->getDoctrine()->getManager()->merge($user);
            $this->getDoctrine()->getManager()->flush();
          }
          
          return new JsonResponse(["status" => 200]);
        }
        catch (NotCorrectDataException $e)
        {
          if ($e->getMessage() === "Incorrect registration data")
          {
            $keys = [];
            if ($registration_form != null)
            {
              $keys = array_keys($registration_form);
            }
            $this->errors[] = "Данные не верны " . implode(", ", $keys);
          }
          else
          {
            $this->errors[] = $e->getMessage();
          }
          
          $fields = $e->getFields();
          if ($fields)
          {
            $this->makeErrorsFromFields($fields);
          }
        }
      }
      
      return new JsonResponse([
        "status" => 400,
        'errors' => $this->errors,
      ]);
    }
    
    
    /**
     * @param $receipts
     *
     * @return array
     */
    public function sortCodes($receipts): array
    {
      $tmp = [];
      $tmp3 = [];
      foreach ($receipts as $receipt)
      {
        $tmp[] = $receipt['id'];
        $tmp3[$receipt['id']] = $receipt;
      }
      sort($tmp, SORT_ASC);
      
      $tmp2 = [];
      foreach ($tmp as $item)
      {
        $tmp2[] = $tmp3[$item];
      }
      $receipts = $tmp2;
      $receipts = array_reverse($receipts);
      
      return $receipts;
    }
    
    /**
     * @return \DateTime[][]
     */
    private function getWeeks()
    {
      $lotteries = $this->getDoctrine()->getRepository('AppBundle:Lottery')->findBy(['prize' => 'certificate_lamoda']);
      $weeks = [];
      $i = 1;
      foreach ($lotteries as $lottery)
      {
        if ($lottery->getStartTime() <= new \DateTime())
        {
          $weeks[$i++] = ['start' => $lottery->getStartTime(), 'end' => $lottery->getEndTime()];
        }
      }
      sort($weeks, SORT_DESC);
      
      return $weeks;
    }
    
  }
