<?php
  
  namespace AppBundle\Repository;
  
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 20.09.17
   * Time: 20:42
   */
  
  use Doctrine\ORM\EntityRepository;
  
  class LogUploadRepository extends EntityRepository
  {
    /**
     * @param $guid String
     *
     * @return int
     */
    public function getLastCount($guid)
    {
      $date = new \DateTime('-1 minute');
      $query = $this->_em->createQueryBuilder()
        ->select('COUNT(l.uuid)')
        ->from('AppBundle:LogUpload', 'l')
        ->where('l.start_time >= :date')
        ->andWhere('l.uuid = :uuid')
        ->setParameter('date', $date )
        ->setParameter('uuid', $guid )
        ->getQuery()->getSingleScalarResult();
  
      return $query;
    }
  
  
    /**
     * @param $guid String
     *
     * @return int
     */
    public function getLastBan($guid)
    {
      $date = new \DateTime('-1 hour');
      $query = $this->_em->createQueryBuilder()
        ->select('COUNT(l.uuid)')
        ->from('AppBundle:LogUpload', 'l')
        ->where('l.start_time >= :date')
        ->andWhere('l.uuid = :uuid')
        ->andWhere('l.rise = 1')
        ->setParameter('date', $date )
        ->setParameter('uuid', $guid )
        ->getQuery()->getSingleScalarResult();
    
      return $query;
    }
  }