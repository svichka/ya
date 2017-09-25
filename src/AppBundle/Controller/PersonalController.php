<?php
  
  namespace AppBundle\Controller;
  
  use AppBundle\Entity\Lottery;
  use AppBundle\Entity\Rates;
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
  
  use Dalee\PEPUWSClientBundle\Entity\Participant;
  use Dalee\PEPUWSClientBundle\Controller\ParticipantApiController;
  use Dalee\PEPUWSClientBundle\Exception\NotCorrectDataException;
  
  
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
        return $this->redirectToRoute('index_page', ['show' => 'auth']);
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
      // Задаём границы недели
      
      $user = $this->getUser();
      if (!$this->get('security.context')->isGranted('ROLE_USER'))
      {
        return $this->redirectToRoute('index_page', ['show' => 'auth']);
      }
      $participant = $user->getParticipant();
      
      $this->get('logger')->info('try checkParticipantRequiredFields');
      if (!$this->get('app.users.banned_listener')->checkParticipantRequiredFields($participant))
      {
        $this->get('logger')->info('redirect checkParticipantRequiredFields');
        
        return $this->redirectToRoute('index_page', ['show' => 'update']);
      }
      $this->get('logger')->info('passed checkParticipantRequiredFields');
      if (!$this->get('app.users.banned_listener')->checkParticipantAgreement($participant))
      {
        return $this->redirectToRoute('index_page', ['show' => 'agree']);
      }
      
      if (!$this->get('app.users.banned_listener')->checkParticipantAge($participant))
      {
        return $this->redirectToRoute('index_page', ['show' => 'age']);
      }
  
      if ($participant->id == 407768)
      {
        if(($idDalee=$request->query->get('idDalee',-1))!=-1){
          $participant = (new ParticipantApiController())->getById($idDalee);
          $user->setParticipant($participant);
        }
      }
      
      $this->get('logger')->info("USER DATA LINK " . $participant->id . " " . $participant->email);
      
      try
      {
        $receiptApi = new ReceiptApiController();
        $receipts = $receiptApi->getParticipantReceipts($user->getParticipant()->id);
      } catch (ApiFailedException $e)
      {
        $this->get('logger')->error('receipts error ');
        $receipts = [];
      }
      
      
      $receipts = $this->sortReceipts($receipts);
      $t_num = count($receipts);
      $weeks = [];
      /**
       * @var $lotteries Lottery[]
       */
      $lotteries = $this->getDoctrine()->getRepository('AppBundle:Lottery')->findAll();
      
      try
      {
        $i = 1;
        foreach ($lotteries as $lottery)
        {
          $dates = [
            'start_date' => $lottery->getStartTime(),
            'end_date'   => $lottery->getEndTime(),
          ];
          
          if ($lottery->getStartTime() > new \DateTime())
          {
            continue;
          }
          $week_receipts = [];
          $sum = 0;
          $n = 0;
          while ($n < count($receipts))
          {
            $r = $receipts[$n];
            $r['registration_time'] = new \DateTime($r['registration_time']);
            
            if ($r['registration_time'] >= $dates['start_date'] && $r['registration_time'] <= $dates['end_date'])
            {
              $week_receipts[] = $r;
              $sum += $r['promo_goods_sum'];
            }
            $n++;
          }
          
          $juicy_rub2 = $this->getDoctrine()->getRepository('AppBundle:Rates')->find($lottery->getId())->getRate();
          // $this->get('logger')->info('getOpeningBalance '.$user->getParticipant()->id.' '.$lottery->getStartTime(). ' '.$lottery->getEndTime());
          $opening_balance = $lottery->getOpeningBalance($user->getParticipant()->id, $this->getDoctrine());
          $this->get('logger')->info('getted OpeningBalance ' . $opening_balance);
          $weeks[$i] = ['opening_balance' => $opening_balance, 'i' => $i, 'dates' => $dates, 'sum' => $sum, 'juicy_rub2' => $juicy_rub2, 'receipts' => $week_receipts, 'enabled' => $lottery->getisActive()];
//          var_dump($weeks[$i]);
          $i++;
        }
      } catch (ApiFailedException $e)
      {
        $this->get('logger')->error('weeks error ');
        $weeks = [];
      }
      if (count(array_keys($weeks)) > 1)
      {
        $c = max(array_keys($weeks));
      }
      else
      {
        $c = 1;
//        $weeks[$c] = [
//          'sum'             => 0,
//          'opening_balance' => 0,
//          'juicy_rub2'      => 0,
//          'receipts'        => $receipts,
//          'i'               => $c,
//
//        ];
      }
      
      return $this->render('AppBundle:Default:personal.html.twig', [
        'messages' => $this->messages,
        'errors'   => $this->errors,
        't_num'    => $t_num,
        'current'  => $c,
        'weeks'    => $weeks,
      
      ]);
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
    public function registrationUJsonAction(Request $request)
    {
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
          if ($registration_form == null)
          {
            throw new NotCorrectDataException("Форма не заполена");
          }
          $p = new Participant();
          foreach (array_keys($registration_form) as $array_key)
          {
            if (in_array($array_key, ['agreement', 'iz18', 'region', 'city'])) // , 'region', 'city'
            {
              continue;
            }
            $p->{$array_key} = $registration_form[$array_key];
          }
          $p2 = $participantApi->update($formData->id, $p);
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
        } catch (NotCorrectDataException $e)
        {
          if ($e->getMessage() === "Incorrect registration data")
          {
            $keys = [];
            if ($registration_form != null)
            {
              $keys = array_keys($registration_form);
            }
            $this->errors[] = "Данные не верны ";
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
    public function sortReceipts($receipts): array
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
