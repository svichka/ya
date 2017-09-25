<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 20.09.17
   * Time: 20:41
   */
  
  namespace AppBundle\Entity;
  
  use Dalee\PEPUWSClientBundle\Controller\LedgerApiController;
  use Doctrine\ORM\Mapping as ORM;
  
  use Doctrine\ORM\Mapping\JoinColumn;
  use Doctrine\ORM\Mapping\ManyToOne;
  use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
  
  /**
   * @ORM\Table(name="app_log_upload")
   * @ORM\Entity(repositoryClass="AppBundle\Repository\LogUploadRepository")
   */
  class LogUpload
  {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    public function __construct()
    {
      $this->rise = 0;
    }
    
    /**
     * @ORM\Column(type="string")
     */
    private $uuid;
    /**
     * @ORM\Column(type="datetime")
     */
    private $start_time;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $rise;
    
    /**
     * @return mixed
     */
    public function getUuid()
    {
      return $this->uuid;
    }
    
    /**
     * @param mixed $uuid
     */
    public function setUuid($uuid)
    {
      $this->uuid = $uuid;
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
    public function getRise()
    {
      return $this->rise;
    }
    
    /**
     * @param mixed $rise
     */
    public function setRise($rise)
    {
      $this->rise = $rise;
    }
  }