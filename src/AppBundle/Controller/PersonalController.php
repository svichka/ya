<?php
  
  namespace AppBundle\Controller;
  
  use AppBundle\Entity\User;
  use Dalee\PEPUWSClientBundle\Controller\CrmReceiptsController;
  use Dalee\PEPUWSClientBundle\Controller\GeoApiController;
  use Dalee\PEPUWSClientBundle\Controller\LedgerApiController;
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
      if (!$this->get('app.users.banned_listener')->checkParticipantAgreement($participant))
      {
        return $this->redirectToRoute('index_page', ['show' => 'agree']);
      }
      
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
      if ($participant->secname == '')
      {
        $participant->secname = '';
      }
      $this->get('logger')->info("USER DATA LINK " . $participant->id . " " . $participant->email);
      
      try
      {
        $receiptApi = new ReceiptApiController();
        $receipts = $receiptApi->getParticipantReceipts($user->getParticipant()->id);
      }
      catch (ApiFailedException $e)
      {
        $this->get('logger')->error('receipts error ');
        $receipts = [];
      }
      $win_receipts = [];
      /**
       * @var \Dalee\PEPUWSClientBundle\Entity\PromocodeApplication[] $promocodes
       */
      $promocodes = (new PromocodeApiController())->getApplicationsByParticipantId($user->getParticipant()->id);
      
      foreach ($promocodes as $application)
      {
        $receipt_guid = $application->getReceipt()['guid'];
        $promoApplications = $application->getPromoApplications();
        foreach ($promoApplications as $promoApplication)
        {
          if (count($promoApplication['prize_options']))
          {
            foreach ($promoApplication['prize_options'] as $prize_option)
            {
              
              $guid = $application->getCode();
              if (!isset($win_receipts[$guid]))
              {
                $win_receipts[$guid] = [];
              }
              $options = $prize_option;
              $win_receipts[$guid][$options['slug']] = $options['slug'];
            }
          }
        }
        $prizeApplications = $application->getPrizeApplications();
        foreach ($prizeApplications as $prizeApplication)
        {
          if (!isset($win_receipts[$guid]))
          {
            $win_receipts[$guid] = [];
          }
          $win_receipts[$guid][$prizeApplication['prize']['slug']] = $prizeApplication['prize']['slug'];
//              var_dump($prizeApplication);
        }
      }
      $receipts = $this->sortReceipts($receipts);
      
      return $this->render('AppBundle:Default:personal.html.twig', [
        'messages'     => $this->messages,
        'errors'       => $this->errors,
        'receipts'     => $receipts,
        'win_receipts' => $win_receipts,
        'participant'  => $participant,]);
    }
    
    
    private
    function makeErrorsFromFields($fields)
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
    
    
    public
    function validate($val, $err)
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
      $password_old = $request->get('password_old', '');
      $password_new = $request->get('password_new', '');
      if ($password_new != '' && $password_old != '')
      {
        try
        {
          $data = [
            "oldpassword" => $password_old,
            "newpassword" => $password_new,
          ];
          $this->get('logger')->info("request new pass object: " . print_r($data, true));
          $participantApi->changePassword($this->getUser()->getParticipant()->id, $data);
          
          return new JsonResponse(["status" => 200]);
        }
        catch (NotCorrectDataException $e)
        {
          $this->get('logger')->error("Error change password NotCorrectDataException " . $e->getMessage());
          throw $e;
        }
        catch (ApiFailedException $e2)
        {
          $this->get('logger')->error("Error change password ApiFailedException ");
          throw $e2;
        }
      }
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
      
      if ($formData->region == '')
      {
        $formData->regionguid = $registration_form['regionguid'];
        $formData->region = $registration_form['region'];
        $this->validate($formData->region, "Выберите регион");
      }
      if ($formData->city == '')
      {
        $formData->cityguid = $registration_form['cityguid'];
        $formData->city = $registration_form['city'];
        $this->validate($formData->city, "Выберите город");
      }
      if ($formData->ismale == '')
      {
        $formData->ismale = $registration_form['ismale'];
        $this->validate($formData->ismale, "Выберите пол");
      }
//      if ($registration_form['agreement'])
//      {
      $formData->isrulesagreed = "Y";
      $formData->ispdagreed = "Y";
      $formData->ismailingagreed = "Y";
//      }
//      else
//      {
//        $this->errors[] = "Согласитесь с условиями";
//        $formData->isrulesagreed = "N";
//        $formData->ispdagreed = "N";
//        $formData->ismailingagreed = "N";
//      }
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
          $fields = ['lastname', 'firstname', 'secname', 'region', 'city', 'regionguid', 'cityguid', 'birthdate', 'email', 'ismale', 'mobilephone'];
          $p2 = $participantApi->getById($formData->id, $fields);
          
          $this->getUser()->setParticipant($p2);
          $participant = $this->getUser()->getParticipant();
          $participant->setCityguid($p2->getCityguid());
          $participant->setCity($p2->getCity());
          $participant->setRegionguid($p2->getRegionguid());
          $participant->setRegion($p2->getRegion());
          
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
     * @Route("/mobile_code/", name="mobile_code")
     */
    public function mobileCodeAction(Request $request)
    {
      $participantApi = new ParticipantApiController();
      $code = $request->request->get('code');
      $participant = $this->getUser()->getParticipant();
      $uI = $this->getDoctrine()->getRepository('AppBundle:User')->find($participant->id);
      if (!$code)
      {
        return new JsonResponse([
          "status" => 400,
          'errors' => "Введите код",
        ]);
      }
      
      $user = $this->getUser()->getParticipant();
      try
      {
        $participantApi->activate('MOBILE', $user->mobilephone, $code);
        $user->isphoneactivated = 'Y';
        $this->getUser()->setParticipant($user);
        $uI->setMobileActivated(1);
        $this->getDoctrine()->getManager()->merge($uI);
        $this->getDoctrine()->getManager()->flush();
        
        return new JsonResponse([
          "status" => 200,
        ]);
      }
      catch (NotCorrectDataException $e)
      {
        return new JsonResponse([
          "status" => 400,
          'errors' => "Код не верный",
        ]);
      }
    }
    
    /**
     * @Route("/mobile_u_json_page/", name="mobile_u_json_page")
     */
    public function mobileUpdateAction(Request $request)
    {
      $this->get('logger')->error("init");
      $participantApi = new ParticipantApiController();
      $mobilephone = $request->request->get('mobilephone');
      $mobilephone = preg_replace("/[^0-9]/", "", $mobilephone);
      if (!$mobilephone)
      {
        return new JsonResponse([
          "status" => 400,
          'errors' => "Введите номер телефона",
        ]);
      }
      $user = $this->getUser()->getParticipant();
      $user->mobilephone = $mobilephone;
      $pp = new Participant();
      $pp->setMobilephone($mobilephone);
      try
      {
        $p2 = $participantApi->update($user->id, $pp);
      }
      catch (NotCorrectDataException $e2)
      {
        $this->get('logger')->error("ERROR update NotCorrectDataException e2 " . $e2->getMessage());
        if ($e2->getMessage() == 'Participant with this email/phone/social account is already registered')
        {
//          return new JsonResponse([
//            "status" => 400,
//            'errors' => "Номер уже привязан к другой учётной записи. Обратитесь в обратную связь.",
//          ]);
          $fields = ['lastname', 'firstname', 'secname', 'region', 'city', 'regionguid', 'cityguid', 'birthdate', 'email', 'ismale', 'mobilephone', 'isphoneactivated'];
          $p_o = $participantApi->getById($user->id, $fields);
          if ($p_o->getMobilephone() == $mobilephone)
          {
            if ($p_o->getIsphoneactivated() == 'Y')
            {
              $uI = $this->getDoctrine()->getRepository('AppBundle:User')->find($user->id);
              $uI->setMobileFilled(1);
              $uI->setMobileActivated(1);
              $this->getDoctrine()->getManager()->merge($uI);
              $this->getDoctrine()->getManager()->flush();
              
              return new JsonResponse([
                "status" => 201,
              ]);
            }
            else
            {
              // Nop идём на отправку смс активации
            }
          }
          else
          {
            return new JsonResponse([
              "status" => 400,
              'errors' => "Номер уже привязан к другой учётной записи. Обратитесь в обратную связь.",
            ]);
          }
        }
        else
        {
          return new JsonResponse([
            "status" => 400,
            'errors' => "Введены не верные данные",
          ]);
        }
      }
      catch (ApiFailedException $e)
      {
        $this->get('logger')->error("ERROR update ApiFailedException e " . $e->getMessage());
        
        return new JsonResponse([
          "status" => 400,
          'errors' => "Внутренняя ошибка привязки номера",
        ]);
      }
      if ($p2->isphoneactivated != 'Y')
      {
        try
        {
          $participantApi->activationGenerate($mobilephone);
        }
        catch (NotCorrectDataException $e2)
        {
          $this->get('logger')->error("ERROR act NotCorrectDataException e2 " . $e2->getMessage());
          if ($e2->getMessage() == 'Incorrect data')
          {
            
            return new JsonResponse([
              "status" => 400,
              'errors' => "Ошибка запроса активации номера. Некорректные данные",
            ]);
          }
          elseif ($e2->getMessage() == 'This email/phone is already activated')
          {
            $uI = $this->getDoctrine()->getRepository('AppBundle:User')->find($user->id);
            $uI->setMobileFilled(1);
            $uI->setMobileActivated(1);
            $this->getDoctrine()->getManager()->merge($uI);
            $this->getDoctrine()->getManager()->flush();
            
            return new JsonResponse([
              "status" => 201,
              'errors' => "Номер уже активирован",
            ]);
          }
          else
          {
            
            return new JsonResponse([
              "status" => 400,
              'errors' => "Ошибка запроса активации номера",
            ]);
          }
        }
        catch (ApiFailedException $e)
        {
          $this->get('logger')->error("ERROR act ApiFailedException e " . $e->getMessage());
          
          return new JsonResponse([
            "status" => 400,
            'errors' => "Внутренняя ошибка запроса активации номера",
          ]);
        }
        $u = $this->getDoctrine()->getRepository('AppBundle:User')->find($user->id);
        $u->setMobileFilled(1);
        $this->getDoctrine()->getManager()->merge($u);
        $this->getDoctrine()->getManager()->flush();
        
        $user->setMobilephone($p2->getMobilephone());
        $user->setIsphoneactivated($p2->getIsphoneactivated());
        $uI = $this->getDoctrine()->getRepository('AppBundle:User')->find($user->id);
        if ($p2->getIsphoneactivated() == 'N')
        {
          $participantApi->activationUpdate($mobilephone);
          
          $uI->setMobileFilled(1);
          $this->getDoctrine()->getManager()->merge($uI);
          $this->getDoctrine()->getManager()->flush();
          
          return new JsonResponse([
            "status" => 300,
          ]);
        }
        else
        {
          $uI->setMobileFilled(1);
          $uI->setMobileActivated(1);
          $this->getDoctrine()->getManager()->merge($uI);
          $this->getDoctrine()->getManager()->flush();
          
          return new JsonResponse([
            "status" => 200,
          ]);
        }
      }
      else
      {
        $uI = $this->getDoctrine()->getRepository('AppBundle:User')->find($user->id);
        $uI->setMobileFilled(1);
        $uI->setMobileActivated(1);
        $this->getDoctrine()->getManager()->merge($uI);
        $this->getDoctrine()->getManager()->flush();
        
        return new JsonResponse([
          "status" => 201,
        ]);
      }
    }
    
    /**
     * @param $receipts
     *
     * @return array
     */
    public
    function sortReceipts($receipts): array
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
    
  }
