<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 24.08.17
   * Time: 23:59
   */
  
  namespace AppBundle\Command;
  
  
  use AppBundle\Controller\FeedbackController;
  use AppBundle\Entity\City;
  use AppBundle\Entity\Lottery;
  use AppBundle\Entity\Rates;
  use AppBundle\Entity\Region;
  use AppBundle\Entity\Theme;
  use Dalee\PEPUWSClientBundle\Controller\FeedbackApiController;
  use Dalee\PEPUWSClientBundle\Controller\GeoApiController;
  use Dalee\PEPUWSClientBundle\Controller\PromoLotteryApiController;
  use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
  use Symfony\Component\Console\Input\InputInterface;
  use Symfony\Component\Console\Output\OutputInterface;
  
  class UpdateFAQCommand extends ContainerAwareCommand
  {
    protected $defaultName;
    
    public function __construct($defaultName)
    {
      $this->defaultName = $defaultName;
      
      parent::__construct();
    }
    
    protected function configure()
    {
      $this
        ->setName('app:update-faq')
        ->setDescription('UpdateFAQCommand.')
        ->setHelp('получение	справочника	тем ОС');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
      // outputs multiple lines to the console (adding "\n" at the end of each line)
      $output->writeln([
        'UpdateFAQCommand',
        '============',
        '',
      ]);
      
      $doctrine = $this->getContainer()->get('doctrine');
      
      $feedbackApiController = new FeedbackApiController();
      
      $forms = $feedbackApiController->getForms();
      $form_id = $forms[0]['id'];
      
      $themes = $feedbackApiController->getThemes(['form_id' => $form_id]);
      var_dump($themes);
      $em = $doctrine->getManager();
      foreach ($themes as $theme)
      {
        /**
         * array(5) {
         *   'id'      => string(4)  "1151"
         *   'form_id' => string(3)  "733"
         *   'code'    => string(6)  "OTHERS"
         *   'name'    => string(12) "Прочее"
         *   'prefix'  => string(1)  "O"
         * }
         */
        $faq = new Theme();
        $faq->setId($theme['id']);
        $faq->setFormId($theme['form_id']);
        $faq->setCode($theme['code']);
        $faq->setName($theme['name']);
        $faq->setPrefix($theme['prefix']);
        $em->merge($faq);
      }
      $em->flush();
//      $regions = $feedbackApiController->getRegionsByCountryCode($countries[0]->getCode());
//
//      $em = $doctrine->getManager();
//      $i = 1;
//      $c = count($regions);
//      foreach ($regions as $region)
//      {
//        $title = $region->getTitle();
//
//        $i++;
//        $r = new Region();
//        $r->setGuid($region->getGuid());
//        $r->setName($title);
//        $r->setShortname($region->getShortname());
//        $em->merge($r);
//        $em->flush();
//
//      }
//
//      $em->flush();
      
      
      $output->writeln('Whoa!');
    }
  }