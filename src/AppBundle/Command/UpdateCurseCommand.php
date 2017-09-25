<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 24.08.17
   * Time: 23:59
   */
  
  namespace AppBundle\Command;
  
  
  use AppBundle\Entity\Lottery;
  use AppBundle\Entity\Rates;
  use AppBundle\Entity\Theme;
  use Dalee\PEPUWSClientBundle\Controller\FeedbackApiController;
  use Dalee\PEPUWSClientBundle\Controller\PromoLotteryApiController;
  use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
  use Symfony\Component\Console\Input\InputInterface;
  use Symfony\Component\Console\Output\OutputInterface;
  
  class UpdateCurseCommand extends ContainerAwareCommand
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
        ->setName('app:update-curse')
        ->setDescription('UpdateCurseCommand.')
        ->setHelp('получение	справочника	коммуникационных	тем.');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
      // outputs multiple lines to the console (adding "\n" at the end of each line)
      $output->writeln([
        'UpdateCurseCommand',
        '============',
        '',
      ]);
      $doctrine = $this->getContainer()->get('doctrine');
      $api = new PromoLotteryApiController();
      
      
      $p = $api->getLotteries('dolka_pie');
      
      foreach ($p as $item)
      {
        $output->writeln("item" . print_r($item, true));
        $l = new Lottery();
        
        $l->setId($item['id']);
        $l->setStartTime(new \DateTime($item['start_time']));
        $l->setEndTime(new \DateTime($item['end_time']));
        $l->setLastUpdated(new \DateTime());
        $l->setIsActive(($item['is_active'] == true) ? 1 : 0);
        
        $u = false;
        if ($l->getStartTime() < new \DateTime() && $l->getisActive())
        {
          $output->writeln('Условие Дениса');
          $u = true;
        }
        else
        {
          $ll = $doctrine->getRepository('AppBundle:Lottery')->find($l->getId());
          
          if ($ll->getisActive() == 1)
          {
            $output->writeln('Условие Дениса 2');
            $u = true;
          }
          else
          {
            $u = false;
          }
          
          $output->writeln('Не обновляем');
        }

//        if ($l->getEndTime() > new \DateTime())
//        {
//          $u = true;
//        }
        if ($u)
        {
          $data = $api->getLotteryStat('dolka_pie', $l->getId());
          
          $r = new Rates();
          $r->setId($l->getId());
          $r->setLastUpdated(new \DateTime());
          
//          if ($l->getId() == 2)
//          {
//            $r->setRate(floor(100000000 / $data['receipts_amount']['net']));
//          }
//          else
//          {
            $r->setRate($data['conversion_rate'] != null ? $data['conversion_rate'] : 0);
//          }
          
          $r->setCount($data['balance']['net'] != null ? $data['balance']['net'] : 0);
          $r->setRemaining($data['balance']['remaining'] != null ? $data['balance']['remaining'] : 0);
          $doctrine->getManager()->merge($r);
          $output->writeln("data" . print_r($data, true));
          $output->writeln("l" . $l->getId() . " r" . $data['conversion_rate']);
        }
        $doctrine->getManager()->merge($l);
      }
      $doctrine->getManager()->flush();
      
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
      
      $em = $doctrine->getManager();
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
      
      
      $output->writeln('Whoa!');
    }
  }