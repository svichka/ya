<?php

namespace AppBundle\Form\Type\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecaptchaType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->setData($options['value']);
	}
	
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
	    $view->vars = array_replace($view->vars, [
			'value' => $options['value'],
		]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'value' => '',
		]);
	}

	public function getBlockPrefix()
	{
		return 'recaptcha';
	}
}