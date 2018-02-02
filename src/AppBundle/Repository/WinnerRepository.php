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
    
    public function findByFio($fio)
    {
      $query = $this->_em->createQueryBuilder()
        ->select('w')
        ->from('AppBundle:Winner', 'w')
        ->where('w.promocode_participant_fio LIKE :fio')
        ->setParameter('fio', "%" . $fio . "%")
        ->getQuery()
        ->getResult();
      
      return $query;
    }
    
    public function findByFioDate($fio, $date)
    {
      $date = \DateTime::createFromFormat("d.m.Y",$date)->format("Y-m-d");
      $query = $this->_em->createQueryBuilder()
        ->select('w')
        ->from('AppBundle:Winner', 'w')
        ->where('w.promocode_participant_fio LIKE :fio')
        ->andWhere('w.win_date LIKE :date')
        ->setParameter('fio', "%" . $fio . "%")
        ->setParameter('date', "%" . $date . "%")
        ->getQuery()
        ->getResult();
      
      return $query;
    }
    
    public function findByDate($date)
    {
      $date = \DateTime::createFromFormat("d.m.Y",$date)->format("Y-m-d");
      $query = $this->_em->createQueryBuilder()
        ->select('w')
        ->from('AppBundle:Winner', 'w')
        ->where('w.win_date LIKE :date')
        ->setParameter('date', "%" . $date . "%")
        ->getQuery()
        ->getResult();
      
      return $query;
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