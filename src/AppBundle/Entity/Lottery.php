<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 25.08.17
   * Time: 0:20
   */
  
  namespace AppBundle\Entity;
  
  use Dalee\PEPUWSClientBundle\Controller\LedgerApiController;
  use Doctrine\ORM\Mapping as ORM;
  
  use Doctrine\ORM\Mapping\JoinColumn;
  use Doctrine\ORM\Mapping\ManyToOne;
  use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
  
  /**
   * @ORM\Table(name="app_lottery")
   * @ORM\Entity(repositoryClass="AppBundle\Repository\LotteryRepository")
   */
  class Lottery
  {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     */
    private $id;
    /**
     * @ORM\Column(type="datetime")
     */
    private $start_time;
    /**
     * @ORM\Column(type="datetime")
     */
    private $end_time;
    /**
     * @ORM\Column(type="datetime")
     */
    private $balance_date;
    /**
     * @ORM\Column(type="integer")
     */
    private $is_runnable;
    /**
     * @ORM\Column(type="integer")
     */
    private $is_ready;
    /**
     * @ORM\Column(type="integer")
     */
    private $is_done;
    /**
     * @ORM\Column(type="string")
     */
    private $prize;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $closed = false;
    /**
     * @ORM\Column(type="datetime")
     */
    private $last_updated;
    
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
    public function getStartTime()
    {
      return $this->start_time;
    }
    
    /**
     * @param mixed $start_time
     */
    public function setStartTime($start_time)
    {
      $this->start_time = $start_time;
    }
    
    /**
     * @return mixed
     */
    public function getEndTime()
    {
      return $this->end_time;
    }
    
    /**
     * @param mixed $end_time
     */
    public function setEndTime($end_time)
    {
      $this->end_time = $end_time;
    }
    
    
    /**
     * @return mixed
     */
    public function getClosed()
    {
      return $this->closed;
    }
    
    /**
     * @param mixed $closed
     */
    public function setClosed($closed)
    {
      $this->closed = $closed;
    }
    
    /**
     * @return mixed
     */
    public function getLastUpdated()
    {
      return $this->last_updated;
    }
    
    /**
     * @param mixed $last_updated
     */
    public function setLastUpdated($last_updated)
    {
      $this->last_updated = $last_updated;
    }
    
    /**
     * @return mixed
     */
    public function getisRunnable()
    {
      return $this->is_runnable;
    }
    
    /**
     * @param mixed $is_runnable
     */
    public function setIsRunnable($is_runnable)
    {
      if ($is_runnable === null)
      {
        $is_runnable = false;
      }
      $this->is_runnable = $is_runnable;
    }
    
    /**
     * @return mixed
     */
    public function getisReady()
    {
      return $this->is_ready;
    }
    
    /**
     * @param mixed $is_ready
     */
    public function setIsReady($is_ready)
    {
      if ($is_ready === null)
      {
        $is_ready = false;
      }
      $this->is_ready = $is_ready;
    }
    
    /**
     * @return mixed
     */
    public function getisDone()
    {
      return $this->is_done;
    }
    
    /**
     * @param mixed $is_done
     */
    public function setIsDone($is_done)
    {
      if ($is_done === null)
      {
        $is_done = false;
      }
      $this->is_done = $is_done;
    }
    
    /**
     * @return mixed
     */
    public function getPrize()
    {
      return $this->prize;
    }
    
    /**
     * @param mixed $prize
     */
    public function setPrize($prize)
    {
      $this->prize = $prize;
    }
  
    /**
     * @return mixed
     */
    public function getBalanceDate()
    {
      return $this->balance_date;
    }
  
    /**
     * @param mixed $balance_date
     */
    public function setBalanceDate($balance_date)
    {
      $this->balance_date = $balance_date;
    }
  }