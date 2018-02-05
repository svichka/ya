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
   * @ORM\Table(name="app_code_history")
   * @ORM\Entity(repositoryClass="AppBundle\Repository\CodeHistoryRepository")
   */
  class CodeHistory
  {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", unique=true)
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     */
    private $code;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $activated;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $status = 0;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $user = 0;
    
    
    /**
     * @return mixed
     */
    public function getCode()
    {
      return $this->code;
    }
    
    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
      $this->code = $code;
    }
    
    /**
     * @return mixed
     */
    public function getActivated()
    {
      return $this->activated;
    }
    
    /**
     * @param mixed $activated
     */
    public function setActivated($activated)
    {
      $this->activated = $activated;
    }
    
    /**
     * @return mixed
     */
    public function getStatus()
    {
      return $this->status;
    }
    
    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
      $this->status = $status;
    }
    
    /**
     * @return mixed
     */
    public function getUser()
    {
      return $this->user;
    }
    
    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
      $this->user = $user;
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