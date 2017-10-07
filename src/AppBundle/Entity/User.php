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
     * @ORM\Column(type="string")
     */
    private $remember;
    
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
  }