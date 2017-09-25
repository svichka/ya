<?php
  
  namespace AppBundle\Form\Type\Participant;
  
  use Doctrine\DBAL\ConnectionException;
  use Symfony\Component\OptionsResolver\OptionsResolver;
  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\Form;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\Form\FormEvent;
  
  use Symfony\Component\Form\Extension\Core\Type\HiddenType;
  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
  use Symfony\Component\Form\CallbackTransformer;
  use Dalee\PEPUWSClientBundle\Controller\GeoApiController;
  use Symfony\Component\Security\Acl\Exception\Exception;

  abstract class ParticipantFieldsFormType extends AbstractType
  {
    
    
    public function callbackGeoFields(FormEvent $event)
    {
      $form = $event->getForm();
      $formData = $event->getData();
      
      $options = ['empty_data' => ''];
      if (static::class == 'AppBundle\Form\Type\Participant\PersonalProfileFormType')
      {
        $options['disabled'] = true;
      }
      $form->add('countrycode', HiddenType::class, $options);
      
      $countryCode = 'RU';
      
      $regions = $this->addRegions($form, $countryCode);
      
      $regionGuid = null;
      if (is_array($formData) && array_key_exists('regionguid', $formData))
      {
        $regionGuid = $formData['regionguid'];
      }
      if (count($regions) == 1)
      {
        $regionGuid = $regions[0]->getGuid();
      }
      $this->addCities($form, $countryCode, $regionGuid);
    }
    
    protected function addRegions(Form $form, $countryCode = null)
    {
      $regions = [];
      if (!is_null($countryCode))
      {
        $regions = $this->getDoctrine()->getRepository('AppBundle:Region')->findAll();
      }
      if (count($regions) == 1)
      {
        $options = ['empty_data' => $regions[0]->getGuid()];
        if (static::class == 'AppBundle\Form\Type\Participant\PersonalProfileFormType')
        {
          $options['disabled'] = true;
        }
        $form->add('regionguid', HiddenType::class, $options);
      }
      else
      {
        $choices = [];
        foreach ($regions as $region)
        {
          
          $title = $region->getName();
          if ($region->getShortname()) {
            $title = $region->getShortName() . ' ' . $title;
          }
  
          $choices[$title] = $region->getGuid();
        }
        $options = ['placeholder' => ' ', 'choices_as_values' => true, 'choices' => $choices];
        if (static::class == 'AppBundle\Form\Type\Participant\PersonalProfileFormType')
        {
          $options['disabled'] = true;
        }
        $form->add('regionguid', ChoiceType::class, $options);
      }
      
      return $regions;
    }
    
    protected function addCities(Form $form, $countryCode = null, $regionGuid = null)
    {
      $cities = [];
      if (!is_null($countryCode) && !is_null($regionGuid))
      {
        $cities = $this->getDoctrine()->getRepository('AppBundle:City')->findByRegion($regionGuid);
      }
      if (count($cities) == 1)
      {
        $options = ['empty_data' => $cities[0]->getGuid()];
        if (static::class == 'AppBundle\Form\Type\Participant\PersonalProfileFormType')
        {
          $options['disabled'] = true;
        }
        $form->add('cityguid', HiddenType::class, $options);
      }
      else
      {
        $choices = [];
        foreach ($cities as $city)
        {
          $title = $city['name'];
          if ($city['short_name']) {
            if($city['short_name']!=='г')
            {
              $title = $city['short_name'] . '. ' . $title;
            }
          }
  
          $choices[$title] = $city['guid'];
        }
        $options = ['placeholder' => ' ', 'choices_as_values' => true, 'choices' => $choices];
        if (static::class == 'AppBundle\Form\Type\Participant\PersonalProfileFormType')
        {
          $options['disabled'] = true;
        }
        $form->add('cityguid', ChoiceType::class, $options);
      }
    }
  }