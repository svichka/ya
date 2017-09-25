<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 25.08.17
   * Time: 0:27
   */
  
  namespace AppBundle\Repository;
  
  
  use Doctrine\ORM\EntityRepository;
  
  class RatesRepository extends EntityRepository
  {
    public function findCurrent()
    {
      $l = $this->getEntityManager()->getRepository('AppBundle:Lottery')->findCurrent();
      
      $l = $this->getEntityManager()->getRepository('AppBundle:Lottery')->find($l[0]);
      $r = $this->getEntityManager()->getRepository('AppBundle:Rates')->find($l->getId());
      
      return $r->getRate();
    }
  
    public function findCount()
    {
      $l = $this->getEntityManager()->getRepository('AppBundle:Lottery')->findCurrent();
  
      $l = $this->getEntityManager()->getRepository('AppBundle:Lottery')->find($l[0]);
      $r = $this->getEntityManager()->getRepository('AppBundle:Rates')->find($l->getId());
  
      return $r->getCount();
    }
  
    public function findRemaining()
    {
      $l = $this->getEntityManager()->getRepository('AppBundle:Lottery')->findCurrent();
    
      $l = $this->getEntityManager()->getRepository('AppBundle:Lottery')->find($l[0]);
      $r = $this->getEntityManager()->getRepository('AppBundle:Rates')->find($l->getId());
    
      return $r->getRemaining();
    }
  }