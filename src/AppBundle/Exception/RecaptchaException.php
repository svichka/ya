<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 28.02.18
   * Time: 23:07
   */
  
  namespace AppBundle\Exception;
  
  use Symfony\Component\Security\Core\Exception\AuthenticationException;

  class RecaptchaException extends AuthenticationException
  {
    public function getMessageKey()
    {
      return 'Рекаптча не заполнена.';
    }
    public function getMessageData()
    {
      return "Рекаптча не заполнена.";
    }
    /**
     * RecaptchaException constructor.
     */
    public function __construct() { }
  }