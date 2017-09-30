<?php
  
  namespace AppBundle\Controller;
  
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
  
  class StaticController extends Base
  {
    /**
     * @Route("/about", name="about_page")
     */
    public function aboutAction()
    {
      setlocale(LC_TIME, "ru_RU.utf8");
      
      $monday = strftime("%e %B", strtotime('monday this week'));
      $sunday = strftime("%e %B", strtotime('sunday this week'));
      
      $m = date('m', strtotime('monday this week'));
      $s = date('m', strtotime('sunday this week'));
      
      if ($m == 8)
      {
        $m = 'а';
      }
      else
      {
        $m = 'я';
      }
      if ($s == 8)
      {
        $s = 'а';
      }
      else
      {
        $s = 'я';
      }
      
      
      return $this->render('AppBundle:Static:about.html.twig', [
        'monday'      => $monday,
        'm'           => $m,
        'sunday'      => $sunday,
        's'           => $s,
      ]);
    }
    
    /**
     * @Route("/faq", name="faq_page")
     */
    public function faqAction()
    {
      return $this->render('AppBundle:Static:faq.html.twig', [
      ]);
    }
    
    /**
     * @Route("/product", name="product_page")
     */
    public function productAction()
    {
      return $this->render('AppBundle:Static:product.html.twig', [
      ]);
    }
  
    /**
     * @Route("/prizes", name="prizes_page")
     */
    public
    function prizesAction()
    {
      return $this->render('AppBundle:Static:prizes.html.twig', [
      ]);
    }
  }
