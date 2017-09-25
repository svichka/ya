<?php

namespace AppBundle\Form\Type\Participant;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Dalee\PEPUWSClientBundle\Validator\Constraints;

class PersonalProfileFormType extends ParticipantFieldsFormType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('email', EmailType::class, ['disabled' => true])
			->add('mobilephone', TextType::class)
			->add('lastname', TextType::class, ['disabled' => true])
			->add('firstname', TextType::class, ['disabled' => true])
			->add('secname', TextType::class, ['disabled' => true])
			->add('ismale', ChoiceType::class, ['choices_as_values' => true, 'choices'  => ['female' => 'N', 'male' => 'Y'], 'expanded' => true, 'multiple' => false, 'disabled' => true])
			->add('birthdate', TextType::class, ['constraints' => [new Constraints\RussianDate()], 'attr' => ['class' => 'js-datepicker'], 'disabled' => true])
			->add('countrycode', HiddenType::class, ['disabled' => true])
			->add('regionguid', ChoiceType::class, ['disabled' => true])
			->add('cityguid', ChoiceType::class, ['disabled' => true])
			->add('district', TextType::class, ['required' => false, 'disabled' => true])
			->add('street', TextType::class, ['required' => false, 'disabled' => true])
			->add('house', TextType::class, ['required' => false, 'disabled' => true])
			->add('building', TextType::class, ['required' => false, 'disabled' => true])
			->add('block', TextType::class, ['disabled' => true])
			->add('flat', TextType::class, ['disabled' => true])
			->add('vk_id', TextType::class, ['required' => false, 'disabled' => true])
			->add('fb_id', TextType::class, ['required' => false, 'disabled' => true])
			->add('ok_id', TextType::class, ['required' => false, 'disabled' => true])
			->add('gp_id', TextType::class, ['required' => false, 'disabled' => true])
			->add('save', SubmitType::class, ['label' => 'Update submit'])
		;

		$builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'callbackGeoFields']);
		$builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'callbackGeoFields']);
	}
}