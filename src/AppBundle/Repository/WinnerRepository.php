<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 15.10.17
   * Time: 0:39
   */
  
  namespace AppBundle\Repository;
  
  use Doctrine\ORM\EntityRepository;
  
  class WinnerRepository extends EntityRepository
  {
    public function findAll()
    {
      return $this->findBy([], ['win_date' => 'DESC']);
    }
    
    public function findByBetween($from, $till)
    {
      $query = $this->_em->createQueryBuilder()
        ->select('w')
        ->from('AppBundle:Winner', 'w')
        ->where('w.win_date >= :from')
        ->andWhere('w.win_date <= :till')
        ->setParameter('from', $from)
        ->setParameter('till', $till)
        ->getQuery()
        ->getResult();
      
      return $query;
    }
  }