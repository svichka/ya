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
  use AppBundle\Entity\Region;
  use AppBundle\Entity\Theme;
  use Dalee\PEPUWSClientBundle\Controller\FeedbackApiController;
  use Dalee\PEPUWSClientBundle\Controller\GeoApiController;
  use Dalee\PEPUWSClientBundle\Controller\ParticipantApiController;
  use Dalee\PEPUWSClientBundle\Controller\PromoLotteryApiController;
  use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
  use Symfony\Component\Console\Input\InputInterface;
  use Symfony\Component\Console\Output\OutputInterface;
  
  class UpdateGenderCommand extends ContainerAwareCommand
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
        ->setName('app:update-gender')
        ->setDescription('UpdateGender.')
        ->setHelp('');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
      // outputs multiple lines to the console (adding "\n" at the end of each line)
      $output->writeln([
        'UpdateGender',
        '============',
        '',
      ]);
      
      $api = new ParticipantApiController();
      $doctrine = $this->getContainer()->get('doctrine');
      /**
       * @var $users \AppBundle\Entity\User[]
       */
      $users = $doctrine->getRepository('AppBundle:User')->findBy(['processed_gender' => 0]);
      /**
       * @var $tmp \Dalee\PEPUWSClientBundle\Entity\Participant[]
       */
      
      foreach ($users as $user)
      {
        $participant = $api->getById($user->getId());
        $firstname = mb_strtolower($participant->getFirstname());
        if (mb_substr($firstname, mb_strlen($firstname) - 1) == 'а' || mb_substr($firstname, mb_strlen($firstname) - 1) == 'я' || $firstname == 'любовь')
        {
          $this->setZ($user, $participant);
        }
        else
        {
          $this->setM($user, $participant);
        }
      }
      
      
      $output->writeln('Whoa!');
    }
    
    /**
     * @param $user        \AppBundle\Entity\User
     * @param $participant \Dalee\PEPUWSClientBundle\Entity\Participant
     */
    private function setM($user, $participant)
    {
      $this->setG($user, $participant, 'Y');
    }
    
    /**
     * @param $user        \AppBundle\Entity\User
     * @param $participant \Dalee\PEPUWSClientBundle\Entity\Participant
     */
    private function setZ($user, $participant)
    {
      $this->setG($user, $participant, 'N');
    }
    
    /**
     * @param $user        \AppBundle\Entity\User
     * @param $participant \Dalee\PEPUWSClientBundle\Entity\Participant
     * @param $gender      string
     */
    private function setG($user, $participant, $gender)
    {
      $em = $this->getContainer()->get('doctrine')->getManager();
      if (empty($participant->getIsmale()))
      {
        $user->setProcessedGender(1);
        $em->merge($user);
        $em->flush();
        
        return;
      }
      if($gender == "N")
      {
        $api = new ParticipantApiController();
        $api->update($participant->id, ['ismale', $gender]);
        $user->setProcessedGender(1);
        $em->merge($user);
        $em->flush();
      }
    }
  }