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
     * @ORM\Column(type="integer")
     */
    private $is_active;
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
    public function getisActive()
    {
      return $this->is_active;
    }
    
    /**
     * @param mixed $is_active
     */
    public function setIsActive($is_active)
    {
      $this->is_active = $is_active;
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
     * @param $id      float
     * @param $doctine \Doctrine\Bundle\DoctrineBundle\Registry
     *
     * @return int
     */
    public function getOpeningBalance($id, $doctine)
    {
      if ($this->is_active == 0)
      {
        $openingBalance = $doctine->getRepository('AppBundle:OpeningBalance')->findOneBy(['user_id' => $id, 'lottery_id' => $this->id]);
        if ($openingBalance == null)
        {
          $ledgerApi = new LedgerApiController();
          $juicy_rub = $ledgerApi->getList($id, 'juicy_rub', $this->start_time, $this->end_time);
          $openingBalance = new OpeningBalance();
          $openingBalance->setBalance($juicy_rub['total']['opening_balance']);
          $openingBalance->setLotteryId($this->id);
          $openingBalance->setUserId($id);
          $doctine->getManager()->merge($openingBalance);
          $doctine->getManager()->flush();
        }
        
        return $openingBalance->getBalance();
      }
      else
      {
        $ledgerApi = new LedgerApiController();
        $juicy_rub = $ledgerApi->getList($id, 'juicy_rub', $this->start_time, $this->end_time);
        return $juicy_rub['total']['opening_balance'];
      }
    }
  }