<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 30.01.18
   * Time: 20:05
   */
  
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
  
  class UpdateController extends Base
  {
    /**
     * @Route("/update_form", name="update_form")
     */
    public function updateAction()
    {
      return $this->render('AppBundle:Modal:update.html.twig');
    }
  }