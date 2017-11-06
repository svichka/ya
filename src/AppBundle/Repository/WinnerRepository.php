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
      return $this->findBy(array(), array('win_date' => 'ASC'));
    }
  }