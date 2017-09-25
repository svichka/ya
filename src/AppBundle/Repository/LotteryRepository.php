<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 25.08.17
   * Time: 0:21
   */
  
  namespace AppBundle\Repository;
  
  
  use Doctrine\ORM\EntityRepository;
  
  class LotteryRepository extends EntityRepository
  {
    public function findCurrent()
    {
      $query = $this->_em->createQueryBuilder()
        ->select('l.id')
        ->from('AppBundle:Lottery', 'l')
        ->where('l.start_time <= CURRENT_TIMESTAMP()')
        ->andWhere('l.end_time >= CURRENT_TIMESTAMP()')
        ->getQuery()
        ->getResult();
      
      return $query;
    }
  }