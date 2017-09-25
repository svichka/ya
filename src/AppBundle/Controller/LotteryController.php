<?php
  
  namespace AppBundle\Controller;
  
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
  use Dalee\PEPUWSClientBundle\Exception\NotCorrectDataException;
  
  use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
  use AppBundle\Security\User\WebserviceUser;
  
  class LotteryController extends Base
  {
    
    
    /**
     * @Route("/lottery/", name="lottery_page")
     */
    public function personalAction(Request $request)
    {
      $user = $this->getUser();
      if (!$this->get('security.context')->isGranted('ROLE_USER'))
      {
        return $this->redirectToRoute('index_page', ['show' => 'auth']);
      }
      $participant = $user->getParticipant();
      if ($participant->id != 407768)
      {
        return $this->redirectToRoute('index_page');
      }
      $promoLotteryApiController = new PromoLotteryApiController();
      $lotteries = $promoLotteryApiController->getLotteries('dolka_pie');
      $tmp = [];
      foreach ($lotteries as $l)
      {
        if ($l['is_active'])
        {
          if (date(strtotime($l['end_time'])) < date('Y.m.d H:i:s'))
          {
            $l['raw'] = $promoLotteryApiController->getLotteryStat('dolka_pie', $l['id']);
            
            $tmp[] = $l;
          }
        }
      }
      $r = '';
      if ($request->query->get('id'))
      {
        try
        {
          $id = $request->query->get('id');
          $r = $promoLotteryApiController->runLottery('dolka_pie', $id);
        } catch (ApiFailedException $e)
        {
          $r = $e->getMessage();
        }
      }
      $dump = print_r($tmp, true);
      
      return $this->render('AppBundle:Lottery:index.html.twig', ['lotteries' => $lotteries, 'r' => $r, 'dump' => $dump]);
    }
    
    
  }
