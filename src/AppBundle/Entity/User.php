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
     * @return mixed
     */
    public function getAgree()
    {
      return $this->agree;
    }
    
    /**
     * @param mixed $agree
     */
    public function setAgree($agree)
    {
      $this->agree = $agree;
    }
    
    /**
     * @return mixed
     */
    public function getId()
    {
      return $this->id;
    }
    
    /**
     * @param mixed $id
     */
    public function setId($id)
    {
      $this->id = $id;
    }
  }