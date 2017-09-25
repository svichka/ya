<?php
  
  namespace AppBundle\Form\Type\Feedback;
  
  use Symfony\Component\Form\Extension\Core\Type\FileType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\Form\FormEvents;
  
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\TextareaType;
  use Symfony\Component\Form\Extension\Core\Type\EmailType;
  use Symfony\Component\Form\Extension\Core\Type\PasswordType;
  use Symfony\Component\Form\Extension\Core\Type\HiddenType;
  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
  use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
  use AppBundle\Form\Type\Field\RecaptchaType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
  
  class NotAuthorizedUserFormType extends FeedbackFieldsFormType
  {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('theme_id', ChoiceType::class)
        ->add('email', EmailType::class)
        ->add('mobile_phone', TextType::class)
        ->add('message', TextareaType::class)
        ->add('file', FileType::class, ['required' => false]);
      global $kernel;
      $recaptchaService = $kernel->getContainer()->get('app.recaptcha');
      if ($recaptchaService && $recaptchaService->isActive())
      {
        $builder->add('recaptcha', RecaptchaType::class, ['mapped' => false, 'value' => $recaptchaService->getPublicKey()]);
      }
      
      $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'addThemes']);
    }
  }