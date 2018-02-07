<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 07.02.18
   * Time: 23:05
   */
  
  namespace AppBundle\Entity;
  
  use Doctrine\Common\Collections\ArrayCollection;
  use Doctrine\ORM\Mapping as ORM;
  
  use Doctrine\ORM\Mapping\JoinColumn;
  use Doctrine\ORM\Mapping\OneToMany;
  use Doctrine\ORM\Mapping\OneToOne;
  use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
  
  /**
   * @ORM\Table(name="app_speed_locks")
   * @ORM\Entity(repositoryClass="AppBundle\Repository\SpeedLockRepository")
   */
  class SpeedLock
  {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", unique=true)
     */
    private $id;
    
    /**
     * @ORM\Column(type="integer", unique=true)
     */
    private $user;
    /**
     * @ORM\Column(type="integer")
     */
    private $count;
    /**
     * @ORM\Column(type="datetime")
     */
    private $till;
    
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
    
    /**
     * @return mixed
     */
    public function getCount()
    {
      return $this->count;
    }
    
    /**
     * @param mixed $count
     */
    public function setCount($count)
    {
      $this->count = $count;
    }
    
    /**
     * @return mixed
     */
    public function getTill()
    {
      return $this->till;
    }
    
    /**
     * @param mixed $till
     */
    public function setTill($till)
    {
      $this->till = $till;
    }
    
    /**
     * @return integer
     */
    public function getUser()
    {
      return $this->user;
    }
    
    /**
     * @param integer
     */
    public function setUser($user)
    {
      $this->user = $user;
    }
    
    
  }