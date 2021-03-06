<?php

namespace AppBundle\Form\Type\Participant;

use Gregwar\CaptchaBundle\Type\CaptchaType;
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
            ->add('email', EmailType::class, ['label' => "Email*", 'attr' => ["placeholder" => "email@email.com", 'onchange' => 'fG(this)']])
            ->add('firstname', TextType::class, ['label' => "Имя*", 'attr' => ["placeholder" => "Имя", 'onkeyup' => 'Ru(this)', 'onchange' => 'fG(this)', 'maxlength' => 30]])// ,"onkeyup"=>"Ru(this);"
            ->add('lastname', TextType::class, ['label' => "Фамилия*", 'attr' => ["placeholder" => "Фамилия", 'onkeyup' => 'Ru(this)', 'onchange' => 'fG(this)', 'maxlength' => 30]])// ,"onkeyup"=>"Ru(this);"
//        ->add('secname', TextType::class, ['label' => "Отчество", 'required' => false, 'attr' => ["placeholder" => "Отчество", 'onkeyup' => 'Ru(this)']])// ,"onkeyup"=>"Ru(this);"
            ->add('birthdate', TextType::class, ['label' => 'Дата рождения*', 'attr' => ['class' => 'js-date', "placeholder" => "18.08.1988", 'onchange' => 'fG(this)']])
            ->add('password', PasswordType::class, ['label' => 'Пароль*', 'mapped' => false, 'attr' => ['plaseholder' => 'Пароль', 'onchange' => 'fG(this)', 'onkeyup' => 'Pass(this)']])
            ->add('confirm_password', PasswordType::class, ['label' => 'Повторите пароль*', 'mapped' => false, 'attr' => ['plaseholder' => 'Повторите пароль', 'onchange' => 'fG(this)', 'onkeyup' => 'Pass(this)']])
            ->add('countrycode', ChoiceType::class, ['label' => 'Страна'])
            ->add('regionguid', ChoiceType::class, [
                    'label'       => 'Регион*',
                    'placeholder' => 'Регион*',
                    'expanded'    => false,
                    'multiple'    => false,
                    'attr'        => ['class' => 'form__select', 'onchange' => 'fG(this)'],
                ]
            )
            ->add('cityguid', ChoiceType::class, ['label' => 'ГОРОД*', 'attr' => ["placeholder" => "город", 'onchange' => 'fG(this)', 'class' => 'form__select'], 'disabled' => true])
            ->add('ismale', ChoiceType::class, [
                'expanded'    => true,
                'multiple'    => false,
                'required'    => true,
                'placeholder' => 'Пол',
                'label'       => 'Пол*',
                'choices'     => [
                    'N' => "Ж",
                    'Y' => "М",
                ],
                'attr'        => ['onchange' => 'fG(this)'],
            ])
            ->add('isageagreed', CheckboxType::class, ['required' => true, 'value' => 'Y', 'label' => 'Я подтверждаю, что мне исполнилось 18 лет на момент участия в Акции', 'attr' => ['class' => 'form__checkbox', 'onchange' => 'fG(this)']])
            ->add('ispdagreed', CheckboxType::class, ['required' => true, 'value' => 'Y', 'label' => 'Я согласен с правилами Акции и пользовательским
соглашением, а также на обработку моих данных', 'attr' => ['class' => 'form__checkbox', 'onchange' => 'fG(this)']]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'callbackGeoFields']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'callbackGeoFields']);

        $builder->add('recaptcha', CaptchaType::class);
//      global $kernel;
//      $recaptchaService = $kernel->getContainer()->get('app.recaptcha');
//      if ($recaptchaService && $recaptchaService->isActive())
//      {
//        $builder->add('recaptcha', RecaptchaType::class, ['mapped' => false, 'value' => $recaptchaService->getPublicKey(), 'attr' => ['onchange' => 'fG(this)']]);
//      }

        $builder->get('isageagreed')->addModelTransformer($this->booleanToYNFormatCallbackTransformer);
        $builder->get('ispdagreed')->addModelTransformer($this->booleanToYNFormatCallbackTransformer);
        $builder->get('ismale')->addModelTransformer($this->booleanToYNFormatCallbackTransformer);
    }
}