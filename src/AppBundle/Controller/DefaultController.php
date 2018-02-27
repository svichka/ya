<?php
  
  namespace AppBundle\Controller;
  
  use AppBundle\Entity\LogUpload;
  use AppBundle\Entity\Receipt;
  use AppBundle\Entity\User;
  use Dalee\PEPUWSClientBundle\Controller\CrmReceiptsController;
  use Dalee\PEPUWSClientBundle\Controller\GeoApiController;
  use Dalee\PEPUWSClientBundle\Controller\LedgerApiController;
  use Dalee\PEPUWSClientBundle\Controller\PromoLotteryApiController;
  use Dalee\PEPUWSClientBundle\Controller\ReceiptApiController;
  use Dalee\PEPUWSClientBundle\Exception\ApiFailedException;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\JsonResponse;
  use Symfony\Component\HttpFoundation\Request;
  use Dalee\PEPUWSClientBundle\Exception\NotCorrectDataException;
  use AppBundle\Form\Type\Participant\RegistrationFormType;
  use AppBundle\Form\Type\Participant\PersonalProfileFormType;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Security\Acl\Exception\Exception;
  use Symfony\Component\Validator\Constraints as Assert;
  use Symfony\Component\Form\Extension\Core\Type\HiddenType;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\EmailType;
  use Symfony\Component\Form\Extension\Core\Type\PasswordType;
  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
  use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
  use Symfony\Component\Form\Extension\Core\Type\ButtonType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
  use Symfony\Component\Form\CallbackTransformer;
  use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
  use Dalee\PEPUWSClientBundle\Entity\Participant;
  use Dalee\PEPUWSClientBundle\Controller\ParticipantApiController;
  
  
  use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
  use AppBundle\Security\User\WebserviceUser;
  
  class DefaultController extends Base
  {
    
    
    private $messages = [];
    private $errors = [];
    private $valid;
    
    /**
     * @Route("/url_test", name="url_test_page")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function urlTestAction(Request $request)
    {
      return new JsonResponse(['url' => $this->container->get('assets.packages')->getUrl('images/ll.png')]);
    }
    
    /**
     * @Route("/promos2", name="promos2_page")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function promos2Action(Request $request)
    {
      $participant = $this->getUser()->getParticipant();
      $data = [
        'p1' => $participant->getIsphoneactivated(),
        'f'  => $participant->getFieldList(),
      ];
      
      $response = new JsonResponse($data);
      $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
      
      return $response;
    }
    
    /**
     * @Route("/promos", name="promos_page")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function promosAction(Request $request)
    {
      
      $data = [];
      if (!$this->get('security.context')->isGranted('ROLE_USER'))
      {
        $data[] = "Not logged in";
      }
      else
      {
        $data = [];
        //$f = $this->getUser()->getParticipant()->getFieldList();
        //$data['participant'] = [];
        //foreach ($f as $item)
        // {
        // $data['participant'][$item] = $this->getUser()->getParticipant()->{$item};
        //}
        
        
        $api = new PromoLotteryApiController();
        
        $receiptApi = new ReceiptApiController();
        $user = $this->getUser()->getParticipant();
        
        $data['dolka_pie'] = $api->getPromoStat('dolka_pie');
        
        $data['receipt'] = $receiptApi->getParticipantReceipts($user->id);
        
        
      }
      $response = new JsonResponse($data);
      $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
      
      return $response;
    }
    
    
    /**
     * @Route("/", name="index_page")
     */
    public function indexAction(Request $request)
    {
      return $this->render('AppBundle:Default:index.html.twig', [
      ]);
    }
    
    /**
     * @Route("/registration", name="registration_page")
     */
    public function registrationAction(Request $request)
    {
      if ($this->getUser())
      {
        return $this->redirectToRoute('personal_page');
      }
      
      $form = $this->createForm(RegistrationFormType::class, new Participant(), ['attr' => ['id' => "registration_form", 'class' => 'form', "autocomplete" => "off"]]);
      
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid())
      {
        if ($request->request->has('ajax') && $request->request->get('ajax') == 'Y')
        {
          
          return $this->render('AppBundle:Default:registration.html.twig', [
            'errors' => $this->errors,
            'form'   => $form->createView(),
          ]);
        }
        $formData = $form->getData();
        $this->get('logger')->error(print_r($formData, true));
        $participantApi = new ParticipantApiController();
        try
        {
          $recaptcha = $this->container->get('app.recaptcha');
          if (!$recaptcha->isSuccess($request))
          {
            throw new NotCorrectDataException('Not correct recaptcha');
          }
//          else
//          {
//            throw new NotCorrectDataException('Success recaptcha');
//          }
          
          if ($form->get('password')->getData() != $form->get('confirm_password')->getData())
          {
            throw new NotCorrectDataException('Confirm password does not match the password');
          }
          if (strlen($form->get('password')->getData()) < 6)
          {
            throw new NotCorrectDataException("Минимальная длина пароля 6 символов");
          }
          if (strlen($form->get('password')->getData()) > 15)
          {
            throw new NotCorrectDataException("Максимальная длина пароля 15 символов");
          }
          /**
           * "data":{"region":"empty","city":"empty"},"
           */
          if ($formData->ispdagreed == "Y")
          {
            $formData->isrulesagreed = "Y";
            $formData->ismailingagreed = "Y";
          }
          else
          {
            $formData->isrulesagreed = "N";
            $formData->ismailingagreed = "N";
            throw new NotCorrectDataException("Согласитесь с условиями");
          }
          
          $dateSArr = explode(".", $formData->birthdate);
          if (count($dateSArr) != 3)
          {
            throw new NotCorrectDataException("Введите верную дату рождения");
          }
          $d = $dateSArr[0];
          $m = $dateSArr[1];
          $y = $dateSArr[2];
          if (!checkdate($m, $d, $y))
          {
            throw new NotCorrectDataException("Введите верную дату рождения");
          }
          
          
          $age = \DateTime::createFromFormat('d.m.Y', $formData->birthdate)
            ->diff(new \DateTime('now'))
            ->y;
          if ($age > 90)
          {
            throw new NotCorrectDataException("Введите верную дату рождения");
          }
          if ($age < 18)
          {
            $this->addFlash('age', 'ok');
            throw new NotCorrectDataException("Ошибка, младше 18 лет");
          }
          
          if ($formData->isageagreed == "N")
          {
            throw new NotCorrectDataException("Подтвердите возраст");
          }
          
          $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneBy(['guid' => $formData->cityguid]);
          $formData->city = $city->getName();
          $region = $this->getDoctrine()->getRepository('AppBundle:Region')->findOneBy(['guid' => $formData->regionguid]);
          $formData->region = $region->getName();
          
          try
          {
            $participant = $participantApi->add($formData, $form->get('password')->getData());
            try
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
                $user->setAgree(1);
                $this->getDoctrine()->getManager()->merge($user);
                $this->getDoctrine()->getManager()->flush();
              }
            }
            catch (Exception $e)
            {
              $this->get('logger')->error("agree log");
            }
          }
          catch (ApiFailedException $e)
          {
            $this->get('logger')->error("ApiFailedException");
            $this->get('logger')->error(print_r($e->getFields(), true));
            $this->get('logger')->error($e->getCode());
            throw new NotCorrectDataException("Ошибка связи с бандлом");
          }
          $this->addFlash('registration', 'ok');
          
          return $this->redirectToRoute('index_page');
        }
        catch (NotCorrectDataException $e)
        {
          $this->errors[] = $e->getMessage();
          $fields = $e->getFields();
          if ($fields)
          {
            $this->makeErrorsFromFields($fields);
          }
          if ($e->getMessage() == 'Participant with this email/phone/social account is already registered')
          {
            $participantApi = new ParticipantApiController();
            try
            {
              $participantApi->recoverPassword($formData->getEmail(), ["channel" => "SMY"]);
            }
            catch (Exception $e)
            {
            
            }
            $this->addFlash('exists', 'ok');
            
            return $this->redirectToRoute('index_page');
          }
        }
      }
      
      return $this->render('AppBundle:Default:registration.html.twig', [
        'errors' => $this->errors,
        'form'   => $form->createView(),
      ]);
    }
    
    /**
     * @Route("/registration_json/", name="registration_json_page")
     */
    public function registrationJsonAction(Request $request)
    {
      $request->getClientIp();
      
      $formData = new Participant();
      $registration_form = $request->request->get('registration_form');
      $this->valid = true;
      
      $formData->email = $registration_form['email'];
      
      $emailConstraint = new EmailConstraint();
      $emailConstraint->message = 'Введите емейл';
      
      $errors = $this->get('validator')->validateValue(
        $formData->email,
        $emailConstraint
      );
      if (count($errors) > 0)
      {
        $this->errors[] = $errors;
        $this->valid = false;
      }
      $formData->firstname = $registration_form['firstname'];
      $this->validate($formData->firstname, "Введите имя");
      $formData->lastname = $registration_form['lastname'];
      $this->validate($formData->lastname, "Введите фамилию");
      $formData->secname = $registration_form['secname'];
//      $formData->mobilephone = $registration_form['mobilephone'];
//      $this->validate($formData->mobilephone, "Введите телефон");
      $formData->birthdate = $registration_form['birthdate'];
      $this->validate($formData->birthdate, "Введите дату рождения");
      $formData->password = $registration_form['password'];
      $this->validate($formData->password, "Введите пароль");
      $formData->confirm_password = $registration_form['confirm_password'];
      $formData->countrycode = $registration_form['countrycode'];
      $formData->regionguid = $registration_form['regionguid'];
      $region = $this->getDoctrine()->getRepository('AppBundle:Region')->findOneBy(['guid' => $registration_form['regionguid']]);
      $this->get('logger')->info('region ' . $region);
      $formData->region = $region;
      $this->validate($formData->region, "Выберите регион");
      $formData->cityguid = $registration_form['cityguid'];
      $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneBy(['guid' => $registration_form['cityguid']]);
      $formData->city = $city;
      $this->get('logger')->info('city ' . $city);
      $this->validate($formData->city, "Выберите город");
      $formData->ismale = $registration_form['ismale'];
      $this->validate($formData->ismale, "Выберите пол");
      if ($registration_form['agreement'])
      {
        $formData->isrulesagreed = "Y";
        $formData->ispdagreed = "Y";
        $formData->ismailingagreed = "Y";
      }
      else
      {
        $this->errors[] = "Согласитесь с условиями";
        $formData->isrulesagreed = "N";
        $formData->ispdagreed = "N";
        $formData->ismailingagreed = "N";
      }
      if ($this->valid)
      {
        $participantApi = new ParticipantApiController();
        try
        {
          $recaptcha = $this->container->get('app.recaptcha');
          if (!$recaptcha->isSuccess($request))
          {
            throw new NotCorrectDataException('Введите рекаптчу');
          }
          if ($registration_form['password'] != $registration_form['confirm_password'])
          {
            throw new NotCorrectDataException('Пароли должны совпадать');
          }
          
          $participantApi->add($formData, $registration_form['password']);
          
          return new JsonResponse(["status" => 200]);
        }
        catch (NotCorrectDataException $e)
        {
          $this->errors[] = $e->getMessage();
          
          $fields = $e->getFields();
          if ($fields)
          {
            $this->makeErrorsFromFields($fields);
          }
        }
        catch (ApiFailedException $e2)
        {
          $this->get('logger')->error('reg error ' . print_r($registration_form, true));
          
          $this->errors[] = "Внутренняя ошибка сервера";
        }
      }
      
      
      return new JsonResponse([
        "status" => 400,
        'errors' => $this->errors,
      ]);
    }
    
    public function validate($val, $err)
    {
      if (trim($val) == "")
      {
        $this->errors[] = $err;
        $this->valid = false;
      }
    }
    
    private function getErrorMessages(\Symfony\Component\Form\Form $form)
    {
      $errors = [];
      
      foreach ($form->getErrors() as $key => $error)
      {
        if ($form->isRoot())
        {
          $errors['#'][] = $error->getMessage();
        }
        else
        {
          $errors[] = $error->getMessage();
        }
      }
      
      foreach ($form->all() as $child)
      {
        if (!$child->isValid())
        {
          $errors[$child->getName()] = $this->getErrorMessages($child);
        }
      }
      
      return $errors;
    }
    
    /**
     * @Route("/check_email_json/", name="check_email_json")
     */
    public function checkEmailJsonAction(Request $request)
    {
      $email = $request->request->get('email');
      try
      {
        if ($email == null || $email == '')
        {
          throw new NotCorrectDataException('email');
        }
        
        $api = new ParticipantApiController();
        if ($api->isLoginUnique($email))
        {
          $data = ['status' => 200];
        }
        else
        {
          $data = ['status' => 400];
        }
      }
      catch (NotCorrectDataException $e)
      {
        $data = ['status' => 500];
      }
      catch (ApiFailedException $e2)
      {
        $this->get('logger')->error('check email error ' . print_r($email, true));
        $data = ['status' => 500];
      }
      
      
      return new JsonResponse($data);
    }
    
    /**
     * @Route("/regions_json/", name="regions_json")
     */
    public
    function regionsJsonAction(Request $request)
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
      
      return new JsonResponse($choices);
    }
    
    /**
     * @Route("/cities_json/", name="cities_json")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public
    function citiesJsonAction(Request $request)
    {
      $regionGuid = $request->get('region');
      
      $cities = $this->getDoctrine()->getRepository('AppBundle:City')->findByRegion($regionGuid);
      $choices = [];
      foreach ($cities as $city)
      {
        $title = $city['name'];
        if ($city['short_name'])
        {
          if ($city['short_name'] !== 'г')
          {
            $title = $city['short_name'] . '. ' . $title;
          }
        }
        
        $choices[$title] = $city['guid'];
      }
      
      return new JsonResponse($choices);
    }
    
    /**
     * @Route("/personal/change_password/", name="change_password_page")
     * @Security("has_role('ROLE_USER', 'ROLE_NOT_ACTIVE_USER')")
     */
    public
    function changePasswordAction(Request $request)
    {
      $user = $this->getUser();
      
      $formBuilder = $this->createFormBuilder([], ['translation_domain' => 'personal'])
        ->add('oldpassword', PasswordType::class)
        ->add('newpassword', PasswordType::class)
        ->add('save', SubmitType::class, ['label' => $this->get('translator')->trans('Change submit')]);
      
      $form = $formBuilder->getForm();
      
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid())
      {
        $formData = $form->getData();
        $participantApi = new ParticipantApiController();
        $participant = $user->getParticipant();
        try
        {
          $participantApi->changePassword($this->getUser()->getParticipant()->getId(), $formData);
          $updatedUser = new WebserviceUser($participant->getEmail(), $formData['newpassword'], '', ['ROLE_USER'], $participant);
          $token = new UsernamePasswordToken($updatedUser, $updatedUser->getPassword(), 'main', $updatedUser->getRoles());
          $this->get('security.context')->setToken($token);
          $this->get('session')->set('_security_main', serialize($token));
          $this->messages[] = 'Success update';
        }
        catch (NotCorrectDataException $e)
        {
          $this->errors[] = $e->getMessage();
        }
      }
      
      return $this->render('AppBundle:Default:personal.html.twig', [
        'messages' => $this->messages,
        'errors'   => $this->errors,
        'form'     => $form->createView(),
      ]);
    }
    
    /**
     * @Route("/send_money/", name="send_money")
     */
    public function sendMoneyAction(Request $request)
    {
      $guid = $request->request->get('guid');
      if (!$guid)
      {
        return new JsonResponse([
          "status" => 400,
          'errors' => "Введите guid",
        ]);
      }
      
      
      // TODO: запрос к Далее
      
      
      // Записываем локально
      $receipt = $this->getDoctrine()->getRepository('AppBundle:Receipt')->findOneByGuid($guid);
      if ($receipt == null)
      {
        $receipt = new Receipt();
        $receipt->setGuid($guid);
      }
      $receipt->setSended(1);
      $this->getDoctrine()->getManager()->merge($receipt);
      $this->getDoctrine()->getManager()->flush();
      
      return JsonResponse::create(
        [
          'status' => 200,
        ]);
    }
    
    /**
     * @Route("/recover_password/", name="recover_password_page")
     */
    public function recoverPasswordAction(Request $request)
    {
      $authenticationUtils = $this->get('security.authentication_utils');
      $error = $authenticationUtils->getLastAuthenticationError();
      
      $lastUsername = $authenticationUtils->getLastUsername();
      if ($this->get('security.context')->isGranted('ROLE_USER'))
      {
        return JsonResponse::create(
          [
            'status'        => 400,
            'last_username' => $lastUsername,
            'error'         => "Вы уже вошли",
          ]);
      }
      
      $login = $request->request->get('email');
      if ($login == '')
      {
        $error = 'Введите емейл';
        
        return JsonResponse::create(
          [
            'status'        => 400,
            'last_username' => $lastUsername,
            'error'         => $error,
          ]);
      }
      $participantApi = new ParticipantApiController();
      try
      {
        $this->get('logger')->debug("Login: " . $login);
        $participantApi->recoverPassword($login, ["channel" => "S"]);
//        $participantApi->dropPassword($login);
        $this->messages[] = 'Success recover';
      }
      catch (NotCorrectDataException $e)
      {
        $error = ['messageKey' => $e->getMessage()];
        switch ($e->getMessage())
        {
          case 'User not found':
            $error = "Пользователь не найден";
            break;
          case 'Incorrect data':
            $error = "Емейл не корректен";
            break;
          default:
            if (
              strpos($e->getMessage(), "Status code") !== false
            )
            {
              $error = "Ошибка востановления пароля";
            }
            break;
        }
        
        return JsonResponse::create(
          [
            'status'        => 400,
            'last_username' => $lastUsername,
            'error'         => $error,
          ]);
      }
      
      return JsonResponse::create(
        [
          'status' => 200,
        ]);
    }
    
    /**
     * @Route("/drop_password/", name="drop_password_page")
     */
    public
    function dropPasswordAction(Request $request)
    {
      $authenticationUtils = $this->get('security.authentication_utils');
      $error = $authenticationUtils->getLastAuthenticationError();
      
      $lastUsername = $authenticationUtils->getLastUsername();
      if ($this->get('security.context')->isGranted('ROLE_USER'))
      {
        return JsonResponse::create(
          [
            'status'        => 400,
            'last_username' => $lastUsername,
            'error'         => "Вы уже вошли",
          ]);
      }
      
      $login = $request->request->get('email');
      $password1 = $request->request->get('password1');
      $password2 = $request->request->get('password2');
      if ($password1 != $password2)
      {
        return JsonResponse::create(
          [
            'status'        => 400,
            'last_username' => $lastUsername,
            'error'         => "Пароли не совпадают",
          ]);
      }
      $participantApi = new ParticipantApiController();
      try
      {
        $participant = $participantApi->dropPassword($login, true, $password1, "S");
        $this->messages[] = 'Пароль успешно установлен';
        
        return JsonResponse::create(
          [
            'status' => 200,
          ]);
      }
      catch (NotCorrectDataException $e)
      {
        if ($e->getMessage() == "User not found")
        {
          $this->errors[] = "Пользователь не найден";
        }
        else
        {
          $this->errors[] = $e->getMessage();
        }
      }
      
      return JsonResponse::create(
        [
          'status'        => 400,
          'last_username' => $lastUsername,
          'error'         => implode(", ", $this->errors),
        ]);
    }
    
    /**
     * @Route("/activate/", name="activate_page")
     */
    public
    function activateAction(Request $request)
    {
      return $this->redirectToRoute('index_page');
//        return $this->redirectToRoute('index_page',['show'=>'code']);
    }
    
    /**
     * @Route("/activate/email/{email}/code/{code}", name="activate_link_page")
     */
    public
    function activateLinkAction($email, $code)
    {
      if ($this->get('security.context')->isGranted('ROLE_USER'))
      {
        return $this->redirectToRoute('personal_page');
      }
      $this->get('logger')->error('activate start ');
      $formData = [
        "login"           => $email,
        "activation_code" => $code,
      ];
      
      $participantApi = new ParticipantApiController();
      try
      {
        $participant = $participantApi->activate(ParticipantApiController::CONTACT_TYPE_EMAIL, $formData['login'], $formData['activation_code']);
        $this->get('logger')->info('activate success ' . print_r($participant, true));
        
        return $this->redirectToRoute('index_page', ['show' => 'done']);
        
      }
      catch (NotCorrectDataException $e)
      {
        $this->errors[] = $e->getMessage();
        $this->get('logger')->error('activate error ' . print_r($e->getMessage(), true));
      }
      catch (ApiFailedException $e2)
      {
        $this->get('logger')->error('activate error ' . print_r($formData, true));
        $this->errors[] = $e2->getMessage();
      }
      
      $renderParameters = [
        'messages' => $this->messages,
        'errors'   => $this->errors,
      ];
      
      return $this->redirectToRoute('index_page', $renderParameters);
    }
    
    /**
     * @Route("/log_upload", name="log_upload")
     */
    public
    function logUploadAction(Request $request)
    {
//      $uuid = $request->get('uuid', null);
      $uuid = $this->getUser()->getParticipant()->guid;
      if ($uuid)
      {
        $count = $this->getDoctrine()->getRepository('AppBundle:LogUpload')->getLastCount($this->getUser()->getParticipant()->guid);
        
        $lu = new LogUpload();
        if ($count >= 15)
        {
          $lu->setRise(1);
        }
        $lu->setUuid($uuid);
        $lu->setStartTime(new \DateTime());
        $this->getDoctrine()->getManager()->persist($lu);
        $this->getDoctrine()->getManager()->flush();
        
        return new JsonResponse(['status' => 200]);
      }
      else
      {
        return new JsonResponse(['status' => 400]);
      }
      
      
    }
    
    /**
     * @Route("/activation_update/", name="activation_update_page")
     */
    public
    function activationUpdateAction(Request $request)
    {
      if ($this->get('security.context')->isGranted('ROLE_USER'))
      {
        return $this->redirectToRoute('personal_page');
      }
      
      $authenticationUtils = $this->get('security.authentication_utils');
      $error = $authenticationUtils->getLastAuthenticationError();
      
      $lastUsername = $authenticationUtils->getLastUsername();
      
      $formBuilder = $this->createFormBuilder([], ['translation_domain' => 'personal'])
        ->add('login', TextType::class, ['data' => $lastUsername])
        //->add('login', EmailType::class, ['data' => $lastUsername, 'constraints' => new Assert\Email()])
        ->add('save', SubmitType::class, ['label' => $this->get('translator')->trans('Activation update submit')]);
      
      $form = $formBuilder->getForm();
      
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid())
      {
        $formData = $form->getData();
        $participantApi = new ParticipantApiController();
        try
        {
          $participant = $participantApi->activationUpdate($formData['login']);
          $this->messages[] = 'Success activation update';
        }
        catch (NotCorrectDataException $e)
        {
          $this->errors[] = $e->getMessage();
        }
      }
      $renderParameters = [
        'messages' => $this->messages,
        'errors'   => $this->errors,
      ];
      if (count($this->messages) == 0)
      {
        $renderParameters['form'] = $form->createView();
      }
      
      return $this->render('AppBundle:Default:personal.html.twig', $renderParameters);
    }
    
    /**
     * @Route("/activation_request/login/{login}", name="activation_request_page")
     */
    public
    function activationRequestAction($login)
    {
      if ($login)
      {
        $participantApi = new ParticipantApiController();
        try
        {
          $participantApi->activationUpdate($login);
          
          return $this->redirectToRoute('index_page', ['show' => 'activationSended']);
        }
        catch (NotCorrectDataException $e)
        {
          return $this->redirectToRoute('index_page', ['show' => 'activationSendError', 'error' => $e->getMessage()]);
        }
      }
      else
      {
        throw new Exception(403);
      }
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
    function checkParticipantRequiredFields(Participant $participant)
    {
      return true;
    }
    
    
    /**
     * @Route("/metro_redirect", name="metro_redirect")
     */
    public
    function metroRedirectAction()
    {
      return $this->redirect("https://www.metro-cc.ru/shop/ru/office/category/%D0%A1%D0%BE%D0%BA%D0%B8_%D0%BD%D0%B5%D0%BA%D1%82%D0%B0%D1%80%D1%8B_%D0%BC%D0%BE%D1%80%D1%81%D1%8B/%D0%A1%D0%BE%D0%BA%D0%B8,%20%D0%BD%D0%B5%D0%BA%D1%82%D0%B0%D1%80%D1%8B,%20%D0%BC%D0%BE%D1%80%D1%81%D1%8B/applyRef?activePage=1&pageSize=24&viewType=2&catTsr=exp&sortBy=DEFAULT&selectRefCmds[0].selected=true&selectRefCmds[0].code=10002&selectRefCmds[0].values[3].selected=true&selectRefCmds[0].values[3].code=4294965411&present_components_cs_footer=true&present_components_cs_contentSubArea=true&present_components_cs_footerLinks=true&present_components_cs_specialNavigation=true&present_components_cs_helpFlyout=true&present_components_cs_categoryPageTeaser=false&present_components_cs_metaNavigation=true&present_components_cs_contentServiceTeaserArea=true&present_components_cs_firstLevelNavigation=true&present_components_cs_footerContact=true&present_components_cs_bread=true&present_components_cs_teaserZone=false");
    }
    
    /**
     * @Route("/win_report", name="win_report")
     */
    public
    function winReportAction()
    {
      $data = [];
      $d = $this->getDoctrine();
      $ws = $this->getDoctrine()->getRepository('AppBundle:Winner')->findAll();
      foreach ($ws as $w)
      {
        $u = $d->getRepository('AppBundle:User')->find($w->getPromocodeParticipantId());
        $data[] = [
          "fio"          => $w->getPromocodeParticipantFio(),
          "pdate"        => $w->getPromocodeParticipantDate(),
          "id"           => $w->getId(),
          "user_crm_id"  => $w->getPromocodeParticipantCrmIdIlp(),
          "user_id"      => $w->getPromocodeParticipantId(),
          "user_guid"    => $w->getPromocodeParticipantGuid(),
          "promocode_id" => $w->getPromocodeId(),
          "receipt_guid" => $w->getReceiptGuid(),
          "preactivated" => $u->getPreMobileStatus(),
          "filled"       => $u->getMobileFilled(),
          "sms"          => $u->getMobileActivated(),
        ];
      }
      
      return $this->render('AppBundle:Default:winreport.html.twig', ['data' => $data]);
    }
    
    /**
     * @Route("/win_json", name="win_json")
     */
    public
    function winJsonAction()
    {
      $data = [];
      $d = $this->getDoctrine();
      $ws = $this->getDoctrine()->getRepository('AppBundle:Winner')->findAll();
//      $api = new ParticipantApiController();
      foreach ($ws as $w)
      {
//        $p = $api->getById($w->getPromocodeParticipantId(),['mobilephone','lastname', 'firstname', 'secname']);
        $u = $d->getRepository('AppBundle:User')->find($w->getPromocodeParticipantId());
        $data[] = [
          "fio"          => $w->getPromocodeParticipantFio(),
          "phone"        => $w->getPromocodeParticipantPhone(),
          "pdate"        => $w->getPromocodeParticipantDate(),
          "id"           => $w->getId(),
          "user_crm_id"  => $w->getPromocodeParticipantCrmIdIlp(),
          "user_id"      => $w->getPromocodeParticipantId(),
          "user_guid"    => $w->getPromocodeParticipantGuid(),
          "promocode_id" => $w->getPromocodeId(),
          "receipt_guid" => $w->getReceiptGuid(),
          "preactivated" => $u->getPreMobileStatus() == '' ? '-' : $u->getPreMobileStatus(),
          "filled"       => $u->getMobileFilled(),
          "sms"          => $u->getMobileActivated(),
          "prize"        => $w->getPromocodeParticipantPrize() == 1 ? "50 рублей" : ($w->getPromocodeParticipantPrize() == 3 ? "Сертификат" : "Главный"),
        ];
      }
      
      return new JsonResponse($data);
    }
  }
