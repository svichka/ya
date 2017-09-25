<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 24.08.17
   * Time: 19:11
   */
  
  namespace AppBundle\Repository;
  
  
  use Doctrine\ORM\EntityRepository;

  class RegionRepository extends EntityRepository
  {
    public function findAll()
    {
      return $this->findBy(array(), array('sort' => 'ASC','name' => 'ASC'));
    }
  }