<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 24.08.17
   * Time: 13:58
   */
  
  namespace AppBundle\Entity;
  
  
  use Doctrine\Common\Collections\ArrayCollection;
  use Doctrine\ORM\Mapping as ORM;

  use Doctrine\ORM\Mapping\OneToMany;
  use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
  
  /**
   * @ORM\Table(name="app_regions")
   * @ORM\Entity(repositoryClass="AppBundle\Repository\RegionRepository")
   * @UniqueEntity("guid")
   */
  class Region
  {
    /**
     * @ORM\Id
     * @ORM\Column(type="string", unique=true)
     */
    private $guid;
    /**
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $short_name;
    /**
     * @ORM\Column(type="integer")
     */
    private $sort = 100;
    
    /**
     * @return mixed
     */
    public function getSort()
    {
      return $this->sort;
    }
    
    /**
     * @param mixed $sort
     */
    public function setSort($sort)
    {
      $this->sort = $sort;
    }
    
    /**
     * @return mixed
     */
    public function getName()
    {
      return $this->name;
    }
    
    /**
     * @param mixed $name
     */
    public function setName($name)
    {
      $this->name = $name;
    }
    
    /**
     * @return mixed
     */
    public function getGuid()
    {
      return $this->guid;
    }
    
    /**
     * @param mixed $guid
     */
    public function setGuid($guid)
    {
      $this->guid = $guid;
    }
  
    /**
     * @return mixed
     */
    public function getShortName()
    {
      return $this->short_name;
    }
  
    /**
     * @param mixed $short_name
     */
    public function setShortName($short_name)
    {
      $this->short_name = $short_name;
    }
  
  }