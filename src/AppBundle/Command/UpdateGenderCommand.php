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
      $names = [
'Nazar',
'Vadim',
'Алан',
'Александр',
'Алексей',
'Аминат',
'Андей',
'Андрей',
'Антон',
'Арсений',
'Артак',
'Артем',
'Асят',
'Барнохон',
'Бато',
'баха',
'Богдан',
'Вадим',
'ваня',
'вапрпо',
'Василий',
'Виктор',
'Виталий',
'Владимир',
'Владислав',
'владыка',
'Вьюгинов',
'Вячеслав',
'Глеб',
'Голиков',
'Григорий',
'Данил',
'Денис',
'дима',
'Дмитрий',
'Дмитрмй',
'Дооранбек',
'Достон',
'Евгений',
'Егор',
'жора',
'Жпхонгир',
'Иван',
'Иг',
'Игорь',
'Ильмир',
'илья',
'Ирлан',
'Карпенко',
'Кирилл',
'Константин',
'костя',
'кузьма',
'леня',
'лёша',
'Макаров',
'Макс',
'Максим',
'Мансур',
'Махбоаб',
'мизеров',
'Михаил',
'Михаленко',
'миша',
'Мурад',
'муса',
'Наиль',
'никита',
'Николай',
'олег',
'Павел',
'Петр',
'Радик',
'Радмир',
'Рамис',
'Родион',
'рома',
'Роман',
'Руслан',
'Рустем',
'Руш',
'саша',
'Сергей',
'серёга',
'серёжа',
'Станислав',
'Стас',
'Сулейман',
'Тенгиз',
'Тимур',
'Фабио',
'Фёдор',
'Филипп',
'Хизир',
'Эдуард',
'Эмиль',
'Юлий',
'юра',
'юрий',
'Ярослав',
      ];
      /**
       * @var $users \AppBundle\Entity\User[]
       */
      $users = $doctrine->getRepository('AppBundle:User')->findBy(['firstname' => $names, 'processed_gender' => 0]);
      $em = $doctrine->getManager();
      /**
       * @var $tmp \Dalee\PEPUWSClientBundle\Entity\Participant[]
       */
      $i = 0;
      foreach ($users as $user)
      {
        $i++;
        try
        {
          $participant = $api->getById($user->getId(), ['ismale']);
          if ($participant->getIsmale() === 'N')
          {
            $api->update($user->getId(), ['ismale' => 'Y']);
            $user->setProcessedGender(1);
            $em->merge($user);
            $em->flush();
            $output->writeln([
              "+ ",
            ]);
          }else{
            $user->setProcessedGender(1);
            $em->merge($user);
            $em->flush();
            $output->writeln([
              "- ",
            ]);
          }
          $output->writeln([
            "OK $i " . $user->getId(),
          ]);
        }
        catch (\Exception $e)
        {
          
          $output->writeln([
            'Err ' . $user->getId(),
            $e->getMessage(),
          ]);
        }
      }
      
      
      $output->writeln('Whoa!');
    }
  }