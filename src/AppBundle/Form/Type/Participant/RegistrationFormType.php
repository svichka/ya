<?php

namespace AppBundle\Form\Type\Participant;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use AppBundle\Form\Type\Field\RecaptchaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegistrationFormType extends ParticipantFieldsFormType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('email', EmailType::class)
      ->add('firstname', TextType::class)
      ->add('lastname', TextType::class)
      ->add('mobilephone', TextType::class)
      ->add('birthdate', TextType::class)
			->add('password', PasswordType::class, ['mapped' => false])
		 	->add('confirm_password', PasswordType::class, ['mapped' => false])
      ->add('countrycode', ChoiceType::class)
      ->add('regionguid', ChoiceType::class)
      ->add('cityguid', ChoiceType::class)
      ->add('ismale', ChoiceType::class, [
        'expanded' => true,
        'multiple' => false,
        'placeholder'=> 'Пол',
        'label'    => 'Пол',
        'choices'  => [
          "Женский" => 'N',
          "Мужской" => 'Y',
        ],
      ])
      
			->add('isrulesagreed', CheckboxType::class, ['value' => 'Y'])
			->add('ispdagreed', CheckboxType::class, ['value' => 'Y'])
			->add('ismailingagreed', CheckboxType::class, ['value' => 'Y', 'required' => false]);
    
    $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'callbackGeoFields']);
    $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'callbackGeoFields']);
  
		global $kernel;
		$recaptchaService = $kernel->getContainer()->get('app.recaptcha');
		if ($recaptchaService && $recaptchaService->isActive()) {
			$builder->add('recaptcha', RecaptchaType::class, ['mapped' => false, 'value' => $recaptchaService->getPublicKey()]);
		}
		$builder->add('save', SubmitType::class, ['label' => 'Registration submit']);
		
		$builder->get('isrulesagreed')->addModelTransformer($this->booleanToYNFormatCallbackTransformer);
		$builder->get('ispdagreed')->addModelTransformer($this->booleanToYNFormatCallbackTransformer);
		$builder->get('ismailingagreed')->addModelTransformer($this->booleanToYNFormatCallbackTransformer);
		$builder->get('ismale')->addModelTransformer($this->booleanToYNFormatCallbackTransformer);
	}
}