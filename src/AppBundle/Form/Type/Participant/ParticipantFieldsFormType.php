<?php
  
  namespace AppBundle\Form\Type\Participant;
  
  use AppBundle\Entity\Region;
  use Dalee\PEPUWSClientBundle\Entity\GeoCountry;
  use Doctrine\ORM\EntityManagerInterface;
  use Symfony\Component\OptionsResolver\OptionsResolver;
  use Symfony\Component\Form\AbstractType;
  use Symfony\Component\Form\Form;
  use Symfony\Component\Form\FormBuilderInterface;
  use Symfony\Component\Form\FormEvent;
  
  use Symfony\Component\Form\Extension\Core\Type\HiddenType;
  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
  use Symfony\Component\Form\CallbackTransformer;
  use Dalee\PEPUWSClientBundle\Controller\GeoApiController;
  
  abstract class ParticipantFieldsFormType extends AbstractType
  {
    protected $geoApi;
    protected $booleanToYNFormatCallbackTransformer;
  
    /**
     * @var $em EntityManagerInterface
     */
    public static $em;
    
    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults([
        'translation_domain' => 'personal',
      ]);
      
      $this->geoApi = new GeoApiController();
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
    
    public function callbackGeoFields(FormEvent $event)
    {
      $form = $event->getForm();
      $formData = $event->getData();
      /**
       * @var $countries GeoCountry[]
       */
      $countries[] = new GeoCountry([
          'id'    => 0,
          'title' => 'Россия',
          'code'  => 'RU',
        ]
      );
      
      $options = ['empty_data' => $countries[0]->getCode()];
      if (static::class == 'AppBundle\Form\Type\Participant\PersonalProfileFormType')
      {
        $options['disabled'] = true;
      }
      $form->add('countrycode', HiddenType::class, $options);
      
      $countryCode = null;
      if (is_array($formData) && array_key_exists('countrycode', $formData) && $formData['countrycode'])
      {
        $countryCode = $formData['countrycode'];
      }
      if (count($countries) == 1)
      {
        $countryCode = $countries[0]->getCode();
      }
      $regions = $this->addRegions($form, 'RU');
      
      $regionGuid = null;
      if (is_array($formData) && array_key_exists('regionguid', $formData))
      {
        $regionGuid = $formData['regionguid'];
      }
      if (count($regions) == 1)
      {
        $regionGuid = $regions[0]->getGuid();
      }
      $this->addCities($form, 'RU', $regionGuid);
    }
    
    protected function addRegions(Form $form, $countryCode = 'RU')
    {
      /**
       * @var $regions Region[]
       */
      $regions = [];
      if (!is_null($countryCode))
      {
        $regions = self::$em->getRepository('AppBundle:Region')->findAll();
      }
      
      $choices = [];
      foreach ($regions as $region)
      {
        $title = $region->getName();
        if ($region->getShortname())
        {
          $title = $region->getShortname() . ' ' . $title;
        }
        $choices[$title] = $region->getGuid();
      }
      $options = ['placeholder' => ' ', 'choices_as_values' => true, 'choices' => $choices,'label'=>'Регион*'];
      if (static::class == 'AppBundle\Form\Type\Participant\PersonalProfileFormType')
      {
        $options['disabled'] = true;
      }
      $form->add('regionguid', ChoiceType::class, $options);
      
      
      return $regions;
    }
    
    protected function addCities(Form $form, $countryCode = null, $regionGuid = null)
    {
      /**
       * @var $cities \AppBundle\Entity\City[]
       */
      $cities = [];
      if (!is_null($countryCode) && !is_null($regionGuid))
      {
//        $cities = $this->geoApi->getCitiesByCountryCodeAndRegionGuid($countryCode, $regionGuid);
        $cities = self::$em->getRepository('AppBundle:City')->findByRegion($regionGuid);
      }
      if (count($cities) == 1)
      {
        $options = ['empty_data' => $cities[0]->getGuid(),'label'=>'Город*'];
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
          if ($city['short_name'])
          {
            $title = $city['short_name'] . '. ' . $title;
          }
          $choices[$title] = $city['guid'];
        }
        $options = ['placeholder' => ' ', 'choices_as_values' => true, 'choices' => $choices,'label'=>'Город*'];
        if (static::class == 'AppBundle\Form\Type\Participant\PersonalProfileFormType')
        {
          $options['disabled'] = true;
        }
        $form->add('cityguid', ChoiceType::class, $options);
      }
    }
  }