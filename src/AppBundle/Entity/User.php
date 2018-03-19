<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 24.08.17
   * Time: 13:58
   */
  
  namespace AppBundle\Entity;
  
  
  use Doctrine\ORM\Mapping as ORM;
  
  use Doctrine\ORM\Mapping\JoinColumn;
  use Doctrine\ORM\Mapping\ManyToOne;
  use Doctrine\ORM\Mapping\OneToOne;
  use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
  
  /**
   * @ORM\Table(name="app_users")
   * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
   */
  class User
  {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     */
    private $id;
    /**
     * @ORM\Column(type="integer")
     */
    private $agree;
    /**
     * @ORM\Column(type="integer",options={"default"=0})
     */
    private $processed_gender;
    /**
     * @ORM\Column(type="string")
     */
    private $remember;
    /**
     * @ORM\Column(type="integer")
     */
    private $mobile_filled;
    /**
     * @ORM\Column(type="integer")
     */
    private $mobile_activated;
    /**
     * @ORM\Column(type="datetime", columnDefinition="DATETIME on update CURRENT_TIMESTAMP")
     */
    private $updated;
    
    
    /**
     * @return integer
     */
    public function getAgree()
    {
      return $this->agree;
    }
    
    /**
     * @param integer $agree
     */
    public function setAgree($agree)
    {
      $this->agree = $agree;
    }
    
    /**
     * @return integer
     */
    public function getId()
    {
      return $this->id;
    }
    
    /**
     * @param integer $id
     */
    public function setId($id)
    {
      $this->id = $id;
    }
    
    public function __construct()
    {
      $this->remember = "";
      $this->mobile_filled = 0;
      $this->mobile_activated = 0;
      $this->processed_gender = 0;
    }
    
    /**
     * @return string
     */
    public function getRemember()
    {
      return $this->remember;
    }
    
    /**
     * @param string $remember
     */
    public function setRemember($remember)
    {
      $this->remember = $remember;
    }
    
    /**
     * @return mixed
     */
    public function getMobileFilled()
    {
      return $this->mobile_filled;
    }
    
    /**
     * @param mixed $mobile_filled
     */
    public function setMobileFilled($mobile_filled)
    {
      $this->mobile_filled = $mobile_filled;
    }
    
    /**
     * @return mixed
     */
    public function getMobileActivated()
    {
      return $this->mobile_activated;
    }
    
    /**
     * @param mixed $mobile_activated
     */
    public function setMobileActivated($mobile_activated)
    {
      $this->mobile_activated = $mobile_activated;
    }
    
    
    /**
     * @return mixed
     */
    public function getUpdated()
    {
      return $this->updated;
    }
    
    /**
     * @param mixed $updated
     */
    public function setUpdated($updated)
    {
      $this->updated = $updated;
    }
  
    /**
     * @return mixed
     */
    public function getProcessedGender()
    {
      return $this->processed_gender;
    }
  
    /**
     * @param mixed $processed_gender
     */
    public function setProcessedGender($processed_gender)
    {
      $this->processed_gender = $processed_gender;
    }
  }