<?php

namespace AppBundle\Form\Type\Feedback;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Dalee\PEPUWSClientBundle\Controller\FeedbackApiController;

abstract class FeedbackFieldsFormType extends AbstractType
{
	protected $feedbackApi;

	public function configureOptions(OptionsResolver $resolver)
	{
		$this->feedbackApi = new FeedbackApiController();
		$resolver->setDefaults([
			'translation_domain' => 'feedback'
		]);
	}

	public function addThemes(FormEvent $event) {
		$form = $event->getForm();
		$forms = $this->feedbackApi->getForms();
		$formParameters = null;
		foreach ($forms as $formData) {
			if (is_null($formParameters)) {
				$formParameters = $formData;
				break;
			}
		}
		if (is_null($formParameters)) {
			return;
		}
		
		$themes = $this->feedbackApi->getThemes(['form_id' => $formParameters['id']]);
		if (count($themes) == 1) {
			$form->add('theme_id', HiddenType::class, ['empty_data'  => $themes[0]['id']]);
		} else {
			$choices = [];
			foreach ($themes as $theme) {
				$choices[$theme['name']] = $theme['id'];
			}
			$form->add('theme_id', ChoiceType::class, ['placeholder' => 'Select theme of feedback', 'choices_as_values' => true, 'choices'  => $choices]);
		}
	}
}