<?php
  
  namespace AppBundle\Controller;
  
  use Dalee\PEPUWSClientBundle\Controller\PromoLotteryApiController;
  use Dalee\PEPUWSClientBundle\Exception\ApiFailedException;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Request;
  
  use AppBundle\Form\Type\Participant\RegistrationFormType;
  use AppBundle\Form\Type\Participant\PersonalProfileFormType;
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
  
  use Dalee\PEPUWSClientBundle\Entity\Participant;
  use Dalee\PEPUWSClientBundle\Controller\ParticipantApiController;
  use Dalee\PEPUWSClientBundle\Exception\NotCorrectDataException;
  
  use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
  use AppBundle\Security\User\WebserviceUser;
  
  class WinnersController extends Base
  {
    /**
     * @Route("/winnners", name="winnners_page")
     */
    public function winnersAction(Request $request)
    {
      /**
       * @var \AppBundle\Entity\Winner[] $winners
       */
      $winners = [];
      $fio = $request->get('fio');
      $date = $request->get('date');
      if ($fio && $date)
      {
        /***
         * @var $tmp_winners \AppBundle\Entity\Winner[]
         */
        $tmp_winners = $this->getDoctrine()->getRepository('AppBundle:Winner')->findByFioDate($fio, $date);
        foreach ($tmp_winners as $tmp_winner)
        {
          $winners[] = [
            'lottery_id' => $tmp_winner->getLotteryId(),
            'fio'        => $tmp_winner->getPromocodeParticipantFio(),
            'email'      => $tmp_winner->getPromocodeParticipantEmail(),
            'prize'      => $tmp_winner->getPrize(),
            'date'       => $tmp_winner->getWinDate(),
          ];
          
        }
        
        return $this->render('AppBundle:Default:winners_plain.html.twig', [
          'winners' => $winners,
          'fio'     => $fio,
          'date'    => $date,
        ]);
      }
      if ($fio)
      {
        /***
         * @var $tmp_winners \AppBundle\Entity\Winner[]
         */
        $tmp_winners = $this->getDoctrine()->getRepository('AppBundle:Winner')->findByFio($fio);
        foreach ($tmp_winners as $tmp_winner)
        {
          $winners[] = [
            'lottery_id' => $tmp_winner->getLotteryId(),
            'fio'        => $tmp_winner->getPromocodeParticipantFio(),
            'email'      => $tmp_winner->getPromocodeParticipantEmail(),
            'prize'      => $tmp_winner->getPrize(),
            'date'       => $tmp_winner->getWinDate(),
          ];
          
        }
        
        return $this->render('AppBundle:Default:winners_plain.html.twig', [
          'winners' => $winners,
          'fio'     => $fio,
          'date'    => '',
        ]);
      }
      if ($date)
      {
        /***
         * @var $tmp_winners \AppBundle\Entity\Winner[]
         */
        $tmp_winners = $this->getDoctrine()->getRepository('AppBundle:Winner')->findByDate($date);
        foreach ($tmp_winners as $tmp_winner)
        {
          $winners[] = [
            'lottery_id' => $tmp_winner->getLotteryId(),
            'fio'        => $tmp_winner->getPromocodeParticipantFio(),
            'email'      => $tmp_winner->getPromocodeParticipantEmail(),
            'prize'      => $tmp_winner->getPrize(),
            'date'       => $tmp_winner->getWinDate(),
          ];
          
        }
        
        return $this->render('AppBundle:Default:winners_plain.html.twig', [
          'winners' => $winners,
          'fio'     => '',
          'date'    => $date,
        ]);
      }
      
      $lotteries = $this->getDoctrine()->getRepository('AppBundle:Lottery')->findBy(['prize' => 'certificate_lamoda']);
      $weeks = [];
      $i = 0;
      foreach ($lotteries as $lottery)
      {
        $i++;
        if ($lottery->getStartTime() < new \DateTime())
        {
          $weeks[$i] = ['start' => $lottery->getStartTime(), 'end' => $lottery->getEndTime(), 'id' => $lottery->getId()];
        }
      }
      sort($weeks, SORT_DESC);
      $i = 0;
      $tmp_weeks = [];
      foreach ($weeks as $week)
      {
        $i++;
        /***
         * @var $tmp_winners \AppBundle\Entity\Winner[]
         */
        $tmp_winners = $this->getDoctrine()->getRepository('AppBundle:Winner')->findByBetween($week['start'], $week['end']);
        $tmp_weeks[$i] = $week;
        foreach ($tmp_winners as $tmp_winner)
        {
          if (!isset($winners[$i]))
          {
            $winners[$i] = [];
          }
          $winners[$i][] = [
            'lottery_id' => $tmp_winner->getLotteryId(),
            'fio'        => $tmp_winner->getPromocodeParticipantFio(),
            'email'      => $tmp_winner->getPromocodeParticipantEmail(),
            'prize'      => $tmp_winner->getPrize(),
            'date'       => $tmp_winner->getWinDate(),
          ];
          
        }
      }
      
      return $this->render('AppBundle:Default:winners.html.twig', [
        'winners' => $winners,
        'weeks'   => $tmp_weeks,
      ]);
    }
  }
