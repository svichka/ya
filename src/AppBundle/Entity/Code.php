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
   * @ORM\Table(name="app_codes")
   * @ORM\Entity(repositoryClass="AppBundle\Repository\CodeRepository")
   */
  class Code
  {
    /**
     * @ORM\Id
     * @ORM\Column(type="string", unique=true)
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $task;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $weekly;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $guaranteed;
    
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
    public function getTask()
    {
      return $this->task;
    }
  
    /**
     * @param mixed $task
     */
    public function setTask($task)
    {
      $this->task = $task;
    }
  
    /**
     * @return mixed
     */
    public function getGuaranteed()
    {
      return $this->guaranteed;
    }
  
    /**
     * @param mixed $guaranteed
     */
    public function setGuaranteed($guaranteed)
    {
      $this->guaranteed = $guaranteed;
    }
  
    /**
     * @return mixed
     */
    public function getWeekly()
    {
      return $this->weekly;
    }
  
    /**
     * @param mixed $weekly
     */
    public function setWeekly($weekly)
    {
      $this->weekly = $weekly;
    }
  }