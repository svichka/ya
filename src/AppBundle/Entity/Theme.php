<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 26.08.17
   * Time: 11:08
   */
  
  namespace AppBundle\Entity;
  
  
  use Doctrine\ORM\Mapping as ORM;
  
  use Doctrine\ORM\Mapping\JoinColumn;
  use Doctrine\ORM\Mapping\ManyToOne;
  use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
  
  /**
   * @ORM\Table(name="app_feedkback_themes")
   * @ORM\Entity(repositoryClass="AppBundle\Repository\ThemeRepository")
   */
  class Theme
  {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     */
    private $id;
    /**
     * @ORM\Column(type="integer")
     */
    private $form_id;
    /**
     * @ORM\Column(type="string")
     */
    private $code;
    /**
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @ORM\Column(type="string")
     */
    private $prefix;
  
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
    public function getFormId()
    {
      return $this->form_id;
    }
  
    /**
     * @param mixed $form_id
     */
    public function setFormId($form_id)
    {
      $this->form_id = $form_id;
    }
  
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
    public function getPrefix()
    {
      return $this->prefix;
    }
  
    /**
     * @param mixed $prefix
     */
    public function setPrefix($prefix)
    {
      $this->prefix = $prefix;
    }
  }