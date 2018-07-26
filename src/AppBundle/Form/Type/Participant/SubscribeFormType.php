<?php
/**
 * Created by PhpStorm.
 * User: svc
 * Date: 23.07.2018
 * Time: 15:05
 */

namespace AppBundle\Form\Type\Participant;


use AppBundle\Form\Type\Field\RecaptchaType;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

class SubscribeFormType extends ParticipantFieldsFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['label' => "E-mail", 'attr' => ['onchange' => 'fG(this)', 'class' => 'form__input']])
            ->add('isageagreed', CheckboxType::class, ['required' => true, 'value' => 'Y', 'label' => 'Я подтверждаю, что мне исполнилось 18 лет на момент участия в Акции', 'attr' => ['class' => 'form__checkbox', 'onchange' => 'fG(this)']])
            ->add('ispdagreed', CheckboxType::class, ['required' => true, 'value' => 'Y', 'label' => 'Я согласен с правилами Акции и пользовательским
соглашением, а также на обработку моих данных', 'attr' => ['class' => 'form__checkbox', 'onchange' => 'fG(this)']]);

        //$builder->add('recaptcha', CaptchaType::class);
      global $kernel;
      $recaptchaService = $kernel->getContainer()->get('app.recaptcha');

        $this->logger = $kernel->getContainer()->get('logger');
      if ($recaptchaService && $recaptchaService->isActive())
      {
          $builder->add('recaptcha', RecaptchaType::class, ['mapped' => false, 'value' => $recaptchaService->getPublicKey(), 'attr' => ['onchange' => 'fG(this)']]);
      }

        $builder->get('isageagreed')->addModelTransformer($this->booleanToYNFormatCallbackTransformer);
        $builder->get('ispdagreed')->addModelTransformer($this->booleanToYNFormatCallbackTransformer);
    }
}