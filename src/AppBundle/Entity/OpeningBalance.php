<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 26.08.17
   * Time: 17:33
   */
  
  namespace AppBundle\Entity;
  
  
  use Dalee\PEPUWSClientBundle\Controller\LedgerApiController;
  use Doctrine\ORM\Mapping as ORM;
  
  use Doctrine\ORM\Mapping\JoinColumn;
  use Doctrine\ORM\Mapping\ManyToOne;
  use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
  
  use Doctrine\ORM\Mapping\UniqueConstraint;
  
  /**
   * @ORM\Table(name="app_open_balances", uniqueConstraints={@UniqueConstraint(name="balance_id", columns={"user_id", "lottery_id"})})
   * @ORM\Entity(repositoryClass="AppBundle\Repository\OpeningBalanceRepository")
   */
  class OpeningBalance
  {
    /** @ORM\Id
     *  @ORM\Column(type="integer")
     */
    private $lottery_id;
    /** @ORM\Id
     *  @ORM\Column(type="integer")
     */
    private $user_id;
    /** @ORM\Column(type="float") */
    private $balance;
    
    /**
     * @return mixed
     */
    public function getLotteryId()
    {
      return $this->lottery_id;
    }
    
    /**
     * @param mixed $lottery_id
     */
    public function setLotteryId($lottery_id)
    {
      $this->lottery_id = $lottery_id;
    }
    
    /**
     * @return mixed
     */
    public function getUserId()
    {
      return $this->user_id;
    }
    
    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
      $this->user_id = $user_id;
    }
    
    /**
     * @return float
     */
    public function getBalance()
    {
      return $this->balance;
    }
    
    /**
     * @param mixed $balance
     */
    public function setBalance($balance)
    {
      $this->balance = $balance;
    }
    
  }