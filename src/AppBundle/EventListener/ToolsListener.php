<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 23.08.17
   * Time: 21:12
   */
  
  namespace AppBundle\EventListener;
  
  
  use AppBundle\Controller\PersonalController;
  use AppBundle\Controller\StaticController;
  use Dalee\PEPUWSClientBundle\Controller\GeoApiController;
  use Dalee\PEPUWSClientBundle\Controller\ParticipantApiController;
  use Dalee\PEPUWSClientBundle\Controller\PromoLotteryApiController;
  use Dalee\PEPUWSClientBundle\Entity\GeoCity;
  use Dalee\PEPUWSClientBundle\Entity\GeoRegion;
  use DateInterval;
  use DateTime;
  use Psr\Log\LoggerInterface;
  use AppBundle\Controller\DefaultController;
  use Symfony\Component\HttpFoundation\JsonResponse;
  use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
  use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
  
  
  class ToolsListener
  {
    private $logger;
    private $start_date;
    
    public function __construct(LoggerInterface $logger, $start_date)
    {
      $this->logger = $logger;
      $this->start_date = $start_date;
    }
    
    public function getWeekByBum($week)
    {
      $start_date = $this->start_date;
      $i = $week - 1;
      $de = $week;
      $s = DateTime::createFromFormat("Y-m-d H:i:s", $start_date);
      $e = DateTime::createFromFormat("Y-m-d H:i:s", $start_date);
      $s->add(new DateInterval("P{$i}W"));
      $e->add(new DateInterval("P{$de}W"));
      $e->sub(new DateInterval('PT1S'));
      $start_date = $s->format("Y-m-d H:i:s");
      $end_date = $e->format("Y-m-d H:i:s");
      
      return ['start_date' => $start_date, 'end_date' => $end_date];
    }
  
    public function showOB($id)
    {
    
    }
  }