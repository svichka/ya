<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 25.08.17
   * Time: 0:20
   */
  
  namespace AppBundle\Entity;
  
  use Doctrine\ORM\Mapping as ORM;
  
  use Doctrine\ORM\Mapping\JoinColumn;
  use Doctrine\ORM\Mapping\ManyToOne;
  use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
  
  /**
   * @ORM\Table(name="app_rates")
   * @ORM\Entity(repositoryClass="AppBundle\Repository\RatesRepository")
   */
  class Rates
  {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     */
    private $id;
    /**
     * @ORM\Column(type="datetime")
     */
    private $last_updated;
    /**
     * @ORM\Column(type="integer")
     */
    private $rate;
    /**
     * @ORM\Column(type="integer")
     */
    private $count;
    /**
     * @ORM\Column(type="integer")
     */
    private $remaining;
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
    public function getRate()
    {
      return $this->rate;
    }
    
    /**
     * @param mixed $rate
     */
    public function setRate($rate)
    {
      $this->rate = $rate;
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
    public function getRemaining()
    {
      return $this->remaining;
    }
  
    /**
     * @param mixed $remaining
     */
    public function setRemaining($remaining)
    {
      $this->remaining = $remaining;
    }
  
  }