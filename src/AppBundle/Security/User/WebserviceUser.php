<?php
  
  namespace AppBundle\Security\User;
  
  use Symfony\Component\Security\Core\User\UserInterface;
  use Symfony\Component\Security\Core\User\EquatableInterface;
  use Symfony\Component\Security\Core\Exception\UserException;
  use Symfony\Component\HttpFoundation\Session\Session;
  use Dalee\PEPUWSClientBundle\Entity\Participant;
  
  class WebserviceUser implements UserInterface, EquatableInterface
  {
    private $username;
    private $password;
    private $salt;
    private $roles;
    
    /** @var  Participant */
    private $participant;
    
    public function __construct($username, $password, $salt, array $roles, $participant = null)
    {
      $this->username = $username;
      $this->password = $password;
      $this->salt = $salt;
      $this->roles = $roles;
      
      if (!($participant instanceof Participant))
      {
        $session = new Session();
        $userToken = unserialize($session->get('_security_main'));
        
        if ($userToken)
        {
          $participant = $userToken->getUser()->getParticipant();
        }
      }
      if ($participant instanceof Participant)
      {
        $this->setParticipant($participant);
        if (in_array($participant->getId(), ['24934', '28399']))
        {
          if (!in_array('ROLE_ADMIN', $this->roles))
          {
            $this->roles[] = 'ROLE_ADMIN';
          }
          
        }
      }
    }
    
    public function getRoles()
    {
      return $this->roles;
    }
    
    public function getPassword()
    {
      return $this->password;
    }
    
    public function getSalt()
    {
      return $this->salt;
    }
    
    public function getUsername()
    {
      return $this->username;
    }
    
    /**
     * @return Participant
     */
    public function getParticipant()
    {
      return $this->participant;
    }
    
    public function setParticipant(Participant $participant)
    {
      $this->participant = $participant;
      
      return $this;
    }
    
    public function eraseCredentials()
    {
    }
    
    public function isEqualTo(UserInterface $user)
    {
      if (!$user instanceof WebserviceUser)
      {
        return false;
      }
      
      // if ($this->password !== $user->getPassword()) {
      // 	return false;
      // }
      
      if ($this->salt !== $user->getSalt())
      {
        return false;
      }
      
      if ($this->username !== $user->getUsername())
      {
        return false;
      }
      
      return true;
    }
  }
