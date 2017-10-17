<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 17.10.17
   * Time: 4:13
   */
  
  namespace AppBundle\Entity;
  
  use Doctrine\Common\Collections\ArrayCollection;
  use Doctrine\ORM\Mapping as ORM;
  
  use Doctrine\ORM\Mapping\OneToMany;
  use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
  
  /**
   * @ORM\Table(name="app_receipts")
   * @ORM\Entity(repositoryClass="AppBundle\Repository\ReceiptRepository")
   * @UniqueEntity("guid")
   */
  class Receipt
  {
    /**
     * @ORM\Id
     * @ORM\Column(type="string", unique=true)
     */
    private $guid;
    /**
     * @ORM\Column(type="integer")
     */
    private $sended = 0;
    
    /**
     * @return mixed
     */
    public function getGuid()
    {
      return $this->guid;
    }
    
    /**
     * @param mixed $guid
     */
    public function setGuid($guid)
    {
      $this->guid = $guid;
    }
    
    /**
     * @return mixed
     */
    public function getSended()
    {
      return $this->sended;
    }
    
    /**
     * @param mixed $sended
     */
    public function setSended($sended)
    {
      $this->sended = $sended;
    }
    
    public function __construct()
    {
      $this->sended = 0;
    }
  }