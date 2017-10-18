<?php
  
  namespace AppBundle\Form\Type\Feedback;
  
  use Symfony\Component\Form\CallbackTransformer;
  use Symfony\Component\Form\Extension\Core\Type\FileType;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\Form\FormEvents;
  
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\TextareaType;
  use Symfony\Component\Form\Extension\Core\Type\EmailType;
  use Symfony\Component\Form\Extension\Core\Type\HiddenType;
  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
  
  class AuthorizedUserFormType extends FeedbackFieldsFormType
  {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
   
      $builder
        ->add('user_id', HiddenType::class)
        ->add('theme_id', ChoiceType::class, ['attr' => ['class' => 'form__select form__select_height_high']])
        ->add('email', EmailType::class, ["disabled" => true, 'attr' => ['class' => 'form__input form__input_height_high']])
        ->add('message', TextareaType::class, ['attr' => ['class' => 'form__textarea']])
        ->add('file', FileType::class, ['required' => false, 'attr' => ['class' => 'form__input form__input_type_file', 'onchange' => "ValidateSize(this)"]])
        ->add('agree', HiddenType::class);
      
      $builder->add('recaptcha', HiddenType::class);
      
      $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'addThemes']);
    }
  }