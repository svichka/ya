<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 04.02.18
   * Time: 16:49
   */
  
  namespace AppBundle\Repository;
  
  use DateTimeZone;
  use Doctrine\ORM\EntityRepository;
  
  class CodeHistoryRepository extends EntityRepository
  {
    public function findLastMinuteCount($user_id)
    {
      $timezone = new DateTimeZone('Europe/Moscow');
      $date = \DateTime::createFromFormat("Y-m-d H:i:s",date("Y-m-d H:i:s",strtotime('-1 minute')), $timezone);
      
      $query = $this->_em->createQueryBuilder()
        ->select('COUNT(c) as cnt')
        ->from('AppBundle:CodeHistory', 'c')
        ->where('c.activated >= :activated')
        ->andWhere('c.user = :user')
        ->setParameter('user', $user_id)
        ->setParameter("activated", $date)
        ->getQuery()->getOneOrNullResult();
      
      return $query;
    }
    
  }