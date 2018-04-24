<?php
/**
 * Created by PhpStorm.
 * User: wrewolf
 * Date: 24.04.18
 * Time: 23:15
 */

namespace AppBundle\Form\Type\Login;

use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class LoginForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAction('/login/');
        $builder->setMethod('POST');
        $builder->setAttribute('class', 'auth-form');
        $builder->add('recaptcha', CaptchaType::class);
    }
}