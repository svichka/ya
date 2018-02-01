<?php
  
  namespace AppBundle\Controller;
  
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\JsonResponse;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
  
  use Dalee\PEPUWSClientBundle\Controller\PromocodeApiController;
  use Dalee\PEPUWSClientBundle\Entity\Prize;
  use Dalee\PEPUWSClientBundle\Exception\NotCorrectDataException;
  use Dalee\PEPUWSClientBundle\Exception\Promocode\AlreadyUsedException;
  
  class PromocodeController extends Base
  {
    private $messages = [];
    private $errors = [];
    private $form = null;
    
    /**
     * @Route("/promocode", name="entering_promocode_page")
     * @Security("has_role('ROLE_USER')")
     */
    public function enteringPromocodeAction(Request $request)
    {
      $user = $this->getUser();
      
      $formBuilder = $this->createFormBuilder([], ['translation_domain' => 'promocode'])
        ->add('promocode', TextType::class)
        ->add('save', SubmitType::class, ['label' => $this->get('translator')->trans('Register promocode')]);
      $form = $formBuilder->getForm();
      
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid())
      {
        $formData = $form->getData();
        $promocodeApi = new PromocodeApiController();
        try
        {
          $promocode = $promocodeApi->addToParticipant($user->getParticipant()->getId(), $formData['promocode']);
          if (count($promocode->getPromoOptions()) == 0)
          {
            throw new NotCorrectDataException('This promocode is not registered in any promotion');
          }
          
          return $this->redirectToRoute('promocode_page', ['id' => $promocode->getId()]);
        }
        catch (AlreadyUsedException $e)
        {
          $this->errors[] = 'Promocode has already been used';
        }
        catch (NotCorrectDataException $e)
        {
          $this->errors[] = $e->getMessage();
        }
      }
      
      return $this->render('AppBundle:Default:promocode.html.twig', [
        'messages' => $this->messages,
        'errors'   => $this->errors,
        'form'     => $form->createView(),
      ]);
    }
    
    private function selectPromotionAction(Request $request, $promocodeApplication)
    {
      $this->messages[] = $this->get('translator')->trans('You have entered promocode', [], 'promocode') . ' ' . $promocodeApplication->getCode();
      $this->messages[] = $this->get('translator')->trans('Please, choose promotion', [], 'promocode') . ':';
      
      $formBuilder = $this->createFormBuilder([], ['translation_domain' => 'promocode']);
      
      $promoOptions = $promocodeApplication->getPromoOptions();
      foreach ($promoOptions as $promoOption)
      {
        $formBuilder->add($promoOption['slug'], SubmitType::class, ['label' => $promoOption['title']]);
      }
      $this->form = $formBuilder->getForm();
      
      $this->form->handleRequest($request);
      if ($this->form->isSubmitted() && $this->form->isValid())
      {
        $formData = $this->form->getData();
        $currentPromotionSlug = null;
        foreach ($promoOptions as $promoOption)
        {
          if ($this->form->get($promoOption['slug'])->isClicked())
          {
            $currentPromotionSlug = $promoOption['slug'];
            break;
          }
        }
        if (is_null($currentPromotionSlug))
        {
          $this->errors[] = 'You don\'t choose promotion';
        }
        $promocodeApi = new PromocodeApiController();
        try
        {
          $promocodeApi->applyToPromotion($this->getUser()->getParticipant()->getId(), $promocodeApplication->getId(), $currentPromotionSlug);
        }
        catch (AlreadyUsedException $e)
        {
          $this->errors[] = 'Promocode has already been used';
        }
        catch (NotCorrectDataException $e)
        {
          $this->errors[] = $e->getMessage();
        }
        
        return $this->redirectToRoute('promocode_page', ['id' => $promocodeApplication->getId()]);
      }
    }
    
    private function selectPrizeAction(Request $request, $promocodeId, $promotionApplication)
    {
      $user = $this->getUser();
      $prizes = [];
      foreach ($promotionApplication['prize_options'] as $prizeData)
      {
        $prizes[] = new Prize($prizeData);
      }
      $this->messages[] = $this->get('translator')->trans('Please, choose prize in promotion', [], 'promocode') . ' ' . $promotionApplication['promo']['title'] . ':';
      
      $formBuilder = $this->createFormBuilder([], ['translation_domain' => 'promocode']);
      foreach ($prizes as $prize)
      {
        $formBuilder->add($prize->getSlug(), SubmitType::class, ['label' => $prize->getTitle()]);
      }
      $this->form = $formBuilder->getForm();
      
      $this->form->handleRequest($request);
      if ($this->form->isSubmitted() && $this->form->isValid())
      {
        $formData = $this->form->getData();
        $promocodeApi = new PromocodeApiController();
        try
        {
          $currentPrizeSlug = null;
          foreach ($prizes as $prize)
          {
            if ($this->form->get($prize->getSlug())->isClicked())
            {
              $currentPrizeSlug = $prize->getSlug();
              break;
            }
          }
          if (is_null($currentPrizeSlug))
          {
            throw new NotCorrectDataException('You don\'t choose promotion');
          }
          $promocodeApi->applyPrize($user->getParticipant()->getId(), $promocodeId, $promotionApplication['promo']['slug'], $currentPrizeSlug);
          
          return $this->redirectToRoute('promocode_page', ['id' => $promocodeId]);
        }
        catch (AlreadyUsedException $e)
        {
          $this->errors[] = 'Promocode has already been used';
        }
        catch (NotCorrectDataException $e)
        {
          $this->errors[] = $e->getMessage();
        }
      }
    }
    
    /**
     * @Route("/promocode/{id}/", name="promocode_page", requirements={"id"="\d+"})
     * @Security("has_role('ROLE_USER')")
     */
    public function promocodeAction(Request $request, $id)
    {
      $promocodeApi = new PromocodeApiController();
      try
      {
        $currentPromocodeApplication = null;
        $promocodeApplications = $promocodeApi->getApplicationsByParticipantId($this->getUser()->getParticipant()->getId());
        foreach ($promocodeApplications as $promocodeApplication)
        {
          if ($promocodeApplication->getId() != $id)
          {
            continue;
          }
          $currentPromocodeApplication = $promocodeApplication;
        }
        if (is_null($currentPromocodeApplication))
        {
          throw new NotCorrectDataException('Promocode is not exist');
        }
        $promoOptions = $currentPromocodeApplication->getPromoOptions();
        $promotionApplications = $currentPromocodeApplication->getPromoApplications();
        $prizeApplications = $currentPromocodeApplication->getPrizeApplications();
        $wonPrizes = [];
        $promotionApplicationsWithNotChosenPrizes = [];
        foreach ($promotionApplications as $promotionApplication)
        {
          if ($promotionApplication['choice'])
          {
            $promotionApplicationsWithNotChosenPrizes[$promotionApplication['promo']['slug']] = $promotionApplication;
          }
        }
        $alreadyWonPrizeList = [];
        foreach ($prizeApplications as $prizeApplication)
        {
          if (array_key_exists($prizeApplication['promo']['slug'], $promotionApplicationsWithNotChosenPrizes))
          {
            unset($promotionApplicationsWithNotChosenPrizes[$prizeApplication['promo']['slug']]);
          }
          $alreadyWonPrizeList[] = $prizeApplication['prize']['title'];
        }
        $selectResult = null;
        if (count($promoOptions) == 0)
        {
          $this->error[] = 'Unknown error: there are no promotions';
        }
        elseif (count($promoOptions) == 1 && count($promotionApplications) == 0)
        {
          $promocodeApi->applyToPromotion($this->getUser()->getParticipant()->getId(), $currentPromocodeApplication->getId(), $promoOptions[0]['slug']);
          
          return $this->redirectToRoute('promocode_page', ['id' => $id]);
        }
        elseif (count($promoOptions) >= 2 && count($promotionApplications) == 0)
        {
          $selectResult = $this->selectPromotionAction($request, $currentPromocodeApplication);
        }
        elseif (count($promotionApplications) == 0)
        {
          $this->error[] = 'Unknown error: there are no promotions';
        }
        elseif (count($promotionApplicationsWithNotChosenPrizes) > 0)
        {
          foreach ($promotionApplicationsWithNotChosenPrizes as $firstPromotionApplication)
          {
            $selectResult = $this->selectPrizeAction($request, $currentPromocodeApplication->getId(), $firstPromotionApplication);
            break;
          }
        }
        elseif (count($alreadyWonPrizeList) == 0)
        {
          $this->messages[] = $this->get('translator')->trans('You has not won', [], 'promocode');
        }
        elseif (count($alreadyWonPrizeList) > 0)
        {
          $this->messages[] = $this->get('translator')->trans('You has won', [], 'promocode') . ' ' . implode(', ', $alreadyWonPrizeList);
        }
        else
        {
          $this->error[] = 'Unknown error';
        }
        if ($selectResult instanceof Response)
        {
          return $selectResult;
        }
      }
      catch (NotCorrectDataException $e)
      {
        $this->errors[] = $e->getMessage();
      }
      $renderParameters = [
        'messages' => $this->messages,
        'errors'   => $this->errors,
      ];
      if ($this->form)
      {
        $renderParameters['form'] = $this->form->createView();
      }
      
      return $this->render('AppBundle:Default:promocode.html.twig', $renderParameters);
    }
    
    /**
     * @Route("/promocode_list/", name="promocode_list_page")
     * @Security("has_role('ROLE_USER')")
     */
    public function promocodeListAction(Request $request)
    {
      $promocodeApi = new PromocodeApiController();
      $promocodeApplications = $promocodeApi->getApplicationsByParticipantId($this->getUser()->getParticipant()->getId());
      
      return $this->render('AppBundle:Default:promocode_list.html.twig', [
        'promocode_applications' => $promocodeApplications,
      ]);
    }
    
    
    /**
     * @Route("/promocode_check", name="promocode_check")
     * @Security("has_role('ROLE_USER')")
     */
    public function promocodeCheckAction(Request $request)
    {
      $response = ['status' => 200];
      $code = $request->request->get('code', $request->get('code', null));
      if ($code === null)
      {
        $response['status'] = 400;
        $response['error'] = "Код не введён";
        
        return new JsonResponse($response);
      }
      $code = $this->getDoctrine()->getRepository('AppBundle:Code')->findOneBy(['code' => $code]);
      if ($code === null || $code->getActivated() === 1)
      {
        $response['status'] = 400;
        $response['error'] = "Код не существует или уже активирован";
        
        return new JsonResponse($response);
      }
      
      return new JsonResponse($response);
    }
    
    /**
     * @Route("/promocode_register", name="promocode_register")
     * @Security("has_role('ROLE_USER')")
     */
    public function promocodeRegisterAction(Request $request)
    {
      $response = ['status' => 200];
      $slugs = [];
      $code = $request->request->get('code', $request->get('code', null));
      $week = $request->request->get('week', $request->get('prize-garant', null));
      $main = $request->request->get('prize-daily', $request->get('prize-daily', null));
      if ($code === null)
      {
        $response['status'] = 400;
        $response['error'] = "Код не введён";
        
        return new JsonResponse($response);
      }
      if ($week === null)
      {
        $response['status'] = 400;
        $response['error'] = "Не выбран еженедельный приз";
        
        return new JsonResponse($response);
      }
      else
      {
        switch ($main)
        {
          case 'll':
            $slugs[] = "moda_lenina_guaranteed";
            break;
          case 'yr':
            $slugs[] = "moda_yves_rocher_guaranteed";
            break;
          case 'lamoda':
            $slugs[] = "moda_lamoda_guaranteed";
            break;
        }
      }
      if ($main === null)
      {
        $response['status'] = 400;
        $response['error'] = "Не выбран главный приз";
        
        return new JsonResponse($response);
      }
      else
      {
        switch ($main)
        {
          case 'll':
            $slugs[] = "moda_lenina_weekly";
            break;
          case 'yr':
            $slugs[] = "moda_yves_rocher_weekly";
            break;
          case 'lamoda':
            $slugs[] = "moda_lamoda_weekly";
            break;
        }
      }
      
      $code = $this->getDoctrine()->getRepository('AppBundle:Code')->findOneBy(['code' => $code]);
      if ($code === null || $code->getActivated() === 1)
      {
        $response['status'] = 400;
        $response['error'] = "Код не существует или уже активирован";
        
        return new JsonResponse($response);
      }
      $slugs = [$week, $main];
      $userId = $this->getUser()->getParticipant()->getId();
      $promocodeApiController = new PromocodeApiController();
      $result = $promocodeApiController->addToParticipantAsync($userId, $code->getCode(), $slugs);
      $code->setActivated(new \DateTime());
      $code->setStatus(1);
      $code->setUser($userId);
      $code->setTask($result);
      $this->getDoctrine()->getManager()->merge($code);
      $this->getDoctrine()->getManager()->flush();
      
      $response['error'] = $result->getStatus();
      $response['status'] = $result->getResponseCode();
      
      return new JsonResponse($response);
    }
  }
