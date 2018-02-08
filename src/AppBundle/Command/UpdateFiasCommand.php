<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 24.08.17
   * Time: 23:59
   */
  
  namespace AppBundle\Command;
  
  
  use AppBundle\Entity\City;
  use AppBundle\Entity\Lottery;
  use AppBundle\Entity\Region;
  use Dalee\PEPUWSClientBundle\Controller\GeoApiController;
  use Dalee\PEPUWSClientBundle\Controller\PromoLotteryApiController;
  use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
  use Symfony\Component\Console\Input\InputInterface;
  use Symfony\Component\Console\Output\OutputInterface;
  
  class UpdateFiasCommand extends ContainerAwareCommand
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
        ->setName('app:update-fias')
        ->setDescription('UpdateFiasCommand.')
        ->setHelp('получение	справочника	ФИАС	тем.');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
      // outputs multiple lines to the console (adding "\n" at the end of each line)
      $output->writeln([
        'UpdateFiasCommand',
        '============',
        '',
      ]);
      
      $doctrine = $this->getContainer()->get('doctrine');
      
      $geoApi = new GeoApiController();
      /**
       * @var $countries \Dalee\PEPUWSClientBundle\Entity\GeoCountry[]
       */
      $countries = $geoApi->getCountries();
      /**
       * @var $regions \Dalee\PEPUWSClientBundle\Entity\GeoRegion[]
       */
      $regions = $geoApi->getRegionsByCountryCode($countries[0]->getCode());
      
      $em = $doctrine->getManager();
      $i = 1;
      $c = count($regions);
      foreach ($regions as $region)
      {
        $title = $region->getTitle();
        
        $i++;
        $r = new Region();
        $r->setGuid($region->getGuid());
        $r->setName($title);
        $r->setShortname($region->getShortname());
        $em->merge($r);
        $em->flush();
  
        /**
         * @var $cities \Dalee\PEPUWSClientBundle\Entity\GeoCity[]
         */
        $cities = $geoApi->getCitiesByCountryCodeAndRegionGuid($countries[0]->getCode(), $region->getGuid());
        
        $cc = count($cities);
        
        $output->writeln("[$i/$c]\t$cc\t$title");
        
        foreach ($cities as $city)
        {
          $output->writeln(print_r($city->getTitle(), true));
          $title = $city->getTitle();
          $c = new City();
          $c->setRegiongiud($r->getGuid());
          $c->setGuid($city->getGuid());
          $c->setName($title);
          $c->setShortname($city->getShortname());
          $output->writeln(print_r($c, true));
          $em->merge($c);
          $em->flush();
        }
      }
      
      
      
      
      $output->writeln('Whoa!');
    }
  }