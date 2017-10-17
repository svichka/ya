<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 17.10.17
   * Time: 4:14
   */
  
  namespace AppBundle\Repository;
  
  
  use Doctrine\ORM\EntityRepository;
  
  class ReceiptRepository extends EntityRepository
  {
    /**
     * @param $guid
     *
     * @return \AppBundle\Entity\Receipt
     */
    public function findOneByGuid($guid)
    {
      $query = $this->_em->createQueryBuilder()
        ->select('r.guid','r.sended')
        ->from('AppBundle:Receipt', 'r')
        ->where('r.guid = :guid')
        ->setParameter("guid", $guid)
        ->getQuery()->getOneOrNullResult();
      
      return $query;
    }
  }