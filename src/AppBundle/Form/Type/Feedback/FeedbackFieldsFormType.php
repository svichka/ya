<?php

namespace AppBundle\Form\Type\Feedback;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Dalee\PEPUWSClientBundle\Controller\FeedbackApiController;

abstract class FeedbackFieldsFormType extends AbstractType
{
  /**
   * @var $em \AppBundle\Entity\Theme[]
   */
	public static $em;
  public $booleanToYNFormatCallbackTransformer;
  
  public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'translation_domain' => 'feedback'
		]);
    $this->booleanToYNFormatCallbackTransformer = new CallbackTransformer(
      function($value)
      {
        return ($value == 'Y') ? true : false;
      },
      function($value)
      {
        return ($value) ? 'Y' : 'N';
      }
    );
	}

	public function addThemes(FormEvent $event) {
		$form = $event->getForm();
	
  
		if (count(static::$em) == 1) {
			$form->add('theme_id', HiddenType::class, ['empty_data'  => static::$em[0]['id']]);
		} else {
			$choices = [];
			foreach (static::$em as $theme) {
				$choices[$theme->getName()] = $theme->getId();
			}
			$form->add('theme_id', ChoiceType::class, ['placeholder' => 'Select theme of feedback', 'choices_as_values' => true, 'choices'  => $choices,'attr' => ['class' => 'form__select form__select_height_high']]);
		}
	}
}