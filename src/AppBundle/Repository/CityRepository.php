<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 24.08.17
   * Time: 20:05
   */
  
  namespace AppBundle\Repository;
  
  
  use Doctrine\ORM\EntityRepository;
  
  class CityRepository extends EntityRepository
  {
    public function findByRegion($regionguid)
    {
      $query = $this->_em->createQueryBuilder()
        ->select('c.regiongiud','c.guid','c.name', 'c.short_name')
        ->from('AppBundle:City', 'c')
        ->where('c.regiongiud = :regionguid')
        ->setParameter("regionguid", $regionguid)
        ->orderBy('c.sort, c.name')
        ->getQuery()
        ->getResult();
      
      return $query;
    }
  
    public function findAll()
    {
      return $this->findBy(array(), array('sort' => 'ASC','name' => 'ASC'));
    }
  }