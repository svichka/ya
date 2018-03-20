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
'Polina',
'Zulina',
'агния',
'аена',
'азалия',
'азиза',
'аида',
'Айгуль',
'айзада',
'албина',
'алевтина',
'александра',
'александровна',
'алексанра',
'алёна',
'алеся',
'алина',
'Алиса',
'алия',
'алла',
'Алмагуль',
'альбина',
'альвина',
'амина',
'анасасия',
'анастасия',
'ангелина',
'андриана',
'Анель',
'анжела',
'анжелика',
'анна',
'антонина',
'анюта',
'аня',
'арина',
'Аришенька',
'Арпинэ',
'Асель',
'асия',
'аяна',
'божена',
'валентина',
'валерия',
'варвара',
'василина',
'василиса',
'венера',
'вера',
'вероника',
'вика',
'Виктори',
'виктория',
'виолета',
'виолетта',
'влада',
'владилена',
'галина',
'Гузель',
'Гульназ',
'гульсина',
'дарина',
'дарья',
'даша',
'дементьева',
'диана',
'дина',
'динара',
'евгения',
'евдокия',
'екатерина',
'елена',
'елизавета',
'жанара',
'жанна',
'заира',
'залина',
'замира',
'зулина',
'илона',
'иляна',
'ина',
'индира',
'инна',
'иотса',
'ира',
'ирена',
'ирина',
'ирма',
'карина',
'катерина',
'катя',
'киселева',
'клавдия',
'кристина',
'кричтина',
'крюкова',
'ксения',
'лана',
'лариса',
'лейла',
'леня',
'лиана',
'лидия',
'лилия',
'любовь',
'людмила',
'люция',
'мадина',
'мазина',
'майя',
'маргарита',
'марина',
'мария',
'маша',
'милана',
'милена',
'надежда',
'надиа',
'наиля',
'настя',
'наталия',
'наталья',
'наташа',
'нестана',
'николаевна',
'нина',
'нурмагомедова',
'озода',
'оксана',
'олеся',
'ольга',
'оля',
'полина',
'равиля',
'раиса',
'раксана',
'ралина',
'Регина',
'резеда',
'рита',
'роза',
'руфия',
'рухшона',
'сабина',
'саманта',
'самира',
'света',
'светлаа',
'светлана',
'силана',
'ситора',
'соника',
'соня',
'софия',
'софья',
'старшова',
'тайра',
'тамара',
'танзиля',
'таня',
'татьяга',
'татьяна',
'тафинтцева',
'тома',
'ульяна',
'фанзиля',
'фания',
'фаузия',
'Феруза',
'чайка',
'шишкина',
'шувалова',
'элеонора',
'элина',
'элла',
'эльвина',
'эльвира',
'эльза',
'эльмира',
'юлечка',
'юлиана',
'юлия',
'юля',
'яна',
'янина',
'янна',
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
          if ($participant->getIsmale() === 'Y')
          {
            $api->update($user->getId(), ['ismale' => 'N']);
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