<?php
  
  namespace AppBundle\Controller;
  
  use Dalee\PEPUWSClientBundle\Controller\PrizeApiController;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
  
  use Dalee\PEPUWSClientBundle\Controller\ParticipantApiController;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\JsonResponse;
  
  class PrizeController extends Base
  {
    /**
     * @Route("/prize_list/", name="prize_list_page")
     * @Security("has_role('ROLE_USER')")
     */
    public function prizeListAction(\Symfony\Component\HttpFoundation\Request $request)
    {
      $user = $this->getUser();
      $participant = $user->getParticipant();
      
      $api = new ParticipantApiController();
      
      
      try
      {
        $prizes = $api->getPrizes(
          $participant->id
        );
        
      }
      catch (\Dalee\PEPUWSClientBundle\Exception\Base $e)
      {
        $prizes = [];
      }
      
      
      return $this->render('AppBundle:Default:prize_list.html.twig', [
        'prizes' => $prizes,
      ]);
    }
    
    /**
     * @Route("/check_prizes", name="check_prizes")
     * @Security("has_role('ROLE_USER')")
     */
    public function checkPrizesAction(\Symfony\Component\HttpFoundation\Request $request)
    {
      $ret = ['lamoda' => 1, 'yr' => 1, 'll' => 0];
      $api = new PrizeApiController();
      /**
       * @var $prizes \Dalee\PEPUWSClientBundle\Entity\Prize[]
       */
      $prizes = $api->getPrizes();
      foreach ($prizes as $prize)
      {
        switch ($prize->getSlug())
        {
          case "code_lenina":
            if ($prize->getRemainingAmount())
            {
              $ret['ll'] = 1;
            }
            break;
          case "code_yves_rocher":
            if ($prize->getRemainingAmount())
            {
              $ret['yr'] = 1;
            }
            break;
          case "code_lamoda":
            if ($prize->getRemainingAmount())
            {
              $ret['lamoda'] = 1;
            }
            break;
        }
      }
      
      return new JsonResponse($ret);
    }
    
  }
