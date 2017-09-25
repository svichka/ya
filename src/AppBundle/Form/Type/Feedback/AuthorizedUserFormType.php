<?php

namespace AppBundle\Form\Type\Feedback;

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
      ->add('email', EmailType::class,["disabled"=>true])
      ->add('mobile_phone', TextType::class,["disabled"=>true])
			->add('theme_id', ChoiceType::class)
			->add('message', TextareaType::class)
      ->add('file', FileType::class, ['required' => false])
		;

		$builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'addThemes']);
	}
}