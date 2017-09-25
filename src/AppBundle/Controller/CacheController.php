<?php
  
  namespace AppBundle\Controller;
  
  use AppBundle\Entity\City;
  use AppBundle\Entity\Region;
  use AppBundle\Entity\Theme;
  use Dalee\PEPUWSClientBundle\Controller\CrmReceiptsController;
  use Dalee\PEPUWSClientBundle\Controller\FeedbackApiController;
  use Dalee\PEPUWSClientBundle\Controller\GeoApiController;
  use Dalee\PEPUWSClientBundle\Controller\LedgerApiController;
  use Dalee\PEPUWSClientBundle\Controller\PromoLotteryApiController;
  use Dalee\PEPUWSClientBundle\Controller\ReceiptApiController;
  use Dalee\PEPUWSClientBundle\Exception\ApiFailedException;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\JsonResponse;
  use Symfony\Component\HttpFoundation\Request;
  use Dalee\PEPUWSClientBundle\Exception\NotCorrectDataException;
  use AppBundle\Form\Type\Participant\RegistrationFormType;
  use AppBundle\Form\Type\Participant\PersonalProfileFormType;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Security\Acl\Exception\Exception;
  use Symfony\Component\Validator\Constraints as Assert;
  use Symfony\Component\Form\Extension\Core\Type\HiddenType;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\EmailType;
  use Symfony\Component\Form\Extension\Core\Type\PasswordType;
  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
  use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
  use Symfony\Component\Form\Extension\Core\Type\ButtonType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
  use Symfony\Component\Form\CallbackTransformer;
  use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
  use Dalee\PEPUWSClientBundle\Entity\Participant;
  use Dalee\PEPUWSClientBundle\Controller\ParticipantApiController;
  
  
  use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
  use AppBundle\Security\User\WebserviceUser;
  
  class CacheController extends Base
  {
    
    
    private $messages = [];
    private $errors = [];
    private $valid;
    
    /**
     * @Route("/regions_populate", name="regions_populate_page")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function regionsPopulateAction(Request $request)
    {
      set_time_limit(0);
      $data = [];
      $user = $this->getUser();
      if (!$this->get('security.context')->isGranted('ROLE_USER'))
      {
//        return new JsonResponse($data);
      }
      
      $geoApi = new GeoApiController();
      /**
       * @var $countries \Dalee\PEPUWSClientBundle\Entity\GeoCountry[]
       */
      $countries = $geoApi->getCountries();
      /**
       * @var $regions \Dalee\PEPUWSClientBundle\Entity\GeoRegion[]
       */
      $regions = $geoApi->getRegionsByCountryCode($countries[0]->getCode());
      
      $choices = [];
      
      $em = $this->getDoctrine()->getManager();
      
      foreach ($regions as $region)
      {
        $title = $region->getTitle();
       
        $r = new Region();
        $r->setGuid($region->getGuid());
        $r->setName($title);
        $r->setShortname($region->getShortname());
        $em->merge($r);
        $em->flush();
        
        $cities = $geoApi->getCitiesByCountryCodeAndRegionGuid($countries[0]->getCode(), $region->getGuid());
        foreach ($cities as $city)
        {
          $title = $city->getTitle();
          
          $c = new City();
          $c->setRegiongiud($r->getGuid());
          $c->setGuid($city->getGuid());
          $c->setName($title);
          $c->setShortname($city->getShortname());
          
          $em->merge($c);
          
        }
      }
      
      $em->flush();
      $response = new JsonResponse($data);
      $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
      
      return $response;
    }
    
    /**
     * @Route("/feedback_populate", name="feedback_populate_page")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function feedbackAction(Request $request)
    {
      $feedbackApi = new FeedbackApiController();
      $forms = $feedbackApi->getForms();
      $formParameters = null;
      foreach ($forms as $form)
      {
        if (is_null($formParameters))
        {
          $formParameters = $form;
          break;
        }
      }
      if (is_null($formParameters))
      {
        $this->errors[] = 'Form is not found';
        
      }
      $em = $this->getDoctrine()->getManager();
      $themes = $feedbackApi->getThemes(['form_id' => $formParameters['id']]);
      $i = 0;
      foreach ($themes as $theme)
      {
        $t = new Theme();
        $t->setId($theme['id']);
        $t->setFormId($theme['form_id']);
        $t->setName($theme['name']);
        $t->setCode($theme['code']);
        $t->setPrefix($theme['prefix']);
        
        $em->merge($t);
        $i++;
      }
      $em->flush();
      $themes = ['updated' => $i];
      
      return new JsonResponse($themes);
    }
  }
