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
			->add('email', EmailType::class,['label'=>"Email*",'attr'=>["placeholder"=>"email@email.com"]])
      ->add('firstname', TextType::class,['label'=>"Имя*",'attr'=>["placeholder"=>"Имя"]])
      ->add('lastname', TextType::class,['label'=>"Фамилия*",'attr'=>["placeholder"=>"Фамилия"]])
      ->add('secname', TextType::class,['label'=>"Отчество", 'attr'=>["placeholder"=>"Отчество"]])
//      ->add('mobilephone', TextType::class,['attr'=>[]])
      ->add('birthdate', TextType::class,['label'=>'Дата рождения*', 'attr'=>['class'=>'form__input_type_date',"placeholder"=>"01.01.1990"]])
			->add('password', PasswordType::class, ['label'=>'Пароль', 'mapped' => false, 'attr'=>['plaseholder'=>'Пароль']])
		 	->add('confirm_password', PasswordType::class, ['label'=>'Повторите пароль', 'mapped' => false, 'attr'=>['plaseholder'=>'Повторите пароль']])
      ->add('countrycode', ChoiceType::class,['label'=>'Страна'])
      ->add('regionguid', ChoiceType::class,['label'=>'РЕГИОН*'])
      ->add('cityguid', ChoiceType::class,['label'=>'ГОРОД*'])
      ->add('ismale', ChoiceType::class, [
        'expanded' => true,
        'multiple' => false,
        'placeholder'=> 'Пол',
        'label'    => 'Пол',
        'choices'  => [
          "Ж" => 'N',
          "М" => 'Y',
        ],
      ])
      
			->add('isageagreed', CheckboxType::class, ['value' => 'Y', 'label'=>'Я подтверждаю, что мне исполнилось 18 лет на момент участия в Акции','attr'=>['class'=>'form__checkbox']])
			->add('ispdagreed', CheckboxType::class, ['value' => 'Y', 'label'=>'Я согласен с правилами акции иa поьзовательским соглашением, а так же на обработку моих данных','attr'=>['class'=>'form__checkbox']])
			;
    
    $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'callbackGeoFields']);
    $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'callbackGeoFields']);
  
		global $kernel;
		$recaptchaService = $kernel->getContainer()->get('app.recaptcha');
		if ($recaptchaService && $recaptchaService->isActive()) {
			$builder->add('recaptcha', RecaptchaType::class, ['mapped' => false, 'value' => $recaptchaService->getPublicKey()]);
		}
//		$builder->add('save', SubmitType::class, ['label' => 'Registration submit']);
		
//		$builder->get('isrulesagreed')->addModelTransformer($this->booleanToYNFormatCallbackTransformer);
		$builder->get('ispdagreed')->addModelTransformer($this->booleanToYNFormatCallbackTransformer);
//		$builder->get('ismailingagreed')->addModelTransformer($this->booleanToYNFormatCallbackTransformer);
		$builder->get('ismale')->addModelTransformer($this->booleanToYNFormatCallbackTransformer);
	}
}