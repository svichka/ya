<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 15.10.17
   * Time: 0:37
   */
  
  namespace AppBundle\Entity;
  
  use Doctrine\ORM\Mapping as ORM;
  
  use Doctrine\ORM\Mapping\JoinColumn;
  use Doctrine\ORM\Mapping\ManyToOne;
  use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
  
  /**
   * @ORM\Table(name="app_winners")
   * @ORM\Entity(repositoryClass="AppBundle\Repository\WinnerRepository")
   */
  class Winner
  {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     */
    private $id;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $receipt_guid;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $is_active;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $is_winner;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $prize_application;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $promocode_id;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $promocode_participant_id;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $promocode_participant_crm_id_ilp;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $promocode_participant_guid;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $promocode_participant_crm_data;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $promocode_participant_fio;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $promocode_participant_phone;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $promocode_participant_date;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $promocode_participant_prize;
    
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
    public function getisWinner()
    {
      return $this->is_winner;
    }
    
    /**
     * @param mixed $is_winner
     */
    public function setIsWinner($is_winner)
    {
      $this->is_winner = $is_winner;
    }
    
    /**
     * @return mixed
     */
    public function getPrizeApplication()
    {
      return $this->prize_application;
    }
    
    /**
     * @param mixed $prize_application
     */
    public function setPrizeApplication($prize_application)
    {
      $this->prize_application = $prize_application;
    }
    
    /**
     * @return mixed
     */
    public function getPromocodeId()
    {
      return $this->promocode_id;
    }
    
    /**
     * @param mixed $promocode_id
     */
    public function setPromocodeId($promocode_id)
    {
      $this->promocode_id = $promocode_id;
    }
    
    /**
     * @return mixed
     */
    public function getPromocodeParticipantId()
    {
      return $this->promocode_participant_id;
    }
    
    /**
     * @param mixed $promocode_participant_id
     */
    public function setPromocodeParticipantId($promocode_participant_id)
    {
      $this->promocode_participant_id = $promocode_participant_id;
    }
    
    /**
     * @return mixed
     */
    public function getPromocodeParticipantCrmIdIlp()
    {
      return $this->promocode_participant_crm_id_ilp;
    }
    
    /**
     * @param mixed $promocode_participant_crm_id_ilp
     */
    public function setPromocodeParticipantCrmIdIlp($promocode_participant_crm_id_ilp)
    {
      $this->promocode_participant_crm_id_ilp = $promocode_participant_crm_id_ilp;
    }
    
    /**
     * @return mixed
     */
    public function getPromocodeParticipantGuid()
    {
      return $this->promocode_participant_guid;
    }
    
    /**
     * @param mixed $promocode_participant_guid
     */
    public function setPromocodeParticipantGuid($promocode_participant_guid)
    {
      $this->promocode_participant_guid = $promocode_participant_guid;
    }
    
    /**
     * @return mixed
     */
    public function getPromocodeParticipantCrmData()
    {
      return $this->promocode_participant_crm_data;
    }
    
    /**
     * @param mixed $promocode_participant_crm_data
     */
    public function setPromocodeParticipantCrmData($promocode_participant_crm_data)
    {
      $this->promocode_participant_crm_data = $promocode_participant_crm_data;
    }
    
    /**
     * @return mixed
     */
    public function getPromocodeParticipantFio()
    {
      return $this->promocode_participant_fio;
    }
    
    /**
     * @param mixed $promocode_participant_fio
     */
    public function setPromocodeParticipantFio($promocode_participant_fio)
    {
      $this->promocode_participant_fio = $promocode_participant_fio;
    }
    
    /**
     * @return mixed
     */
    public function getPromocodeParticipantPrize()
    {
      return $this->promocode_participant_prize;
    }
    
    /**
     * @param mixed $promocode_participant_prize
     */
    public function setPromocodeParticipantPrize($promocode_participant_prize)
    {
      $this->promocode_participant_prize = $promocode_participant_prize;
    }
    
    /**
     * @return mixed
     */
    public function getPromocodeParticipantDate()
    {
      return $this->promocode_participant_date;
    }
    
    /**
     * @param mixed $promocode_participant_date
     */
    public function setPromocodeParticipantDate($promocode_participant_date)
    {
      $this->promocode_participant_date = $promocode_participant_date;
    }
  
    /**
     * @return mixed
     */
    public function getReceiptGuid()
    {
      return $this->receipt_guid;
    }
  
    /**
     * @param mixed $receipt_guid
     */
    public function setReceiptGuid($receipt_guid)
    {
      $this->receipt_guid = $receipt_guid;
    }
  
    /**
     * @return mixed
     */
    public function getPromocodeParticipantPhone()
    {
      return $this->promocode_participant_phone;
    }
  
    /**
     * @param mixed $promocode_participant_phone
     */
    public function setPromocodeParticipantPhone($promocode_participant_phone)
    {
      $this->promocode_participant_phone = $promocode_participant_phone;
    }
  
  
  }