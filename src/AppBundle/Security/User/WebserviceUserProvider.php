<?php
  
  namespace AppBundle\Security\User;
  
  use AppBundle\Security\User\WebserviceUser;
  use Dalee\PEPUWSClientBundle\Controller\ParticipantApiController;
  use Dalee\PEPUWSClientBundle\Exception\NotCorrectDataException;
  use Dalee\PEPUWSClientBundle\Exception\UserIsNotActiveException;
  use Psr\Log\LoggerInterface;
  use Symfony\Component\Security\Acl\Exception\Exception;
  use Symfony\Component\Security\Core\User\UserProviderInterface;
  use Symfony\Component\Security\Core\User\UserInterface;
  use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
  use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Session\Session;
  
  class WebserviceUserProvider implements UserProviderInterface
  {
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    
    public function loadUserByUsername($username)
    {
      $this->logger->info("Login");
      $request = Request::createFromGlobals();
      
      $password = $request->request->get('_password');
      if (!$password)
      {
        $this->logger->info("Login by Session");
        $session = new Session();
        $userToken = unserialize($session->get('_security_main'));
        if ($userToken)
        {
          return $userToken->getUser();
        }
        
        return new WebserviceUser($username, '', '', ['ROLE_USER']);
      }
      $this->logger->info("Login by Password");
      $participantApi = new ParticipantApiController();
      try
      {
        
        $participant = $participantApi->getByCredentials2(['login' => $username, 'password' => $password]);
        
        if ($participant->isemailactivated !== "Y")
        {
          $participant = null;
          $roles = ['ROLE_NOT_ACTIVE_USER'];
          $roles[] = 'ROLE_NOT_ACTIVE_USER_NOT_ACTIVE_EMAIL';
          
          return new WebserviceUser($username, $password, '', $roles);
        }
      } catch (UserIsNotActiveException $e)
      {
        $roles = ['ROLE_NOT_ACTIVE_USER'];
        
        $loginStatus = $e->getLoginStatus();
        
        $this->logger->error(print_r($loginStatus, true));
        $this->logger->error(print_r($e->getMessage(), true));
        
        if (array_key_exists('email_status', $loginStatus) && $loginStatus['email_status'] == 'N')
        {
          $roles[] = 'ROLE_NOT_ACTIVE_USER_NOT_ACTIVE_EMAIL';
        }
        else
        {
//        $roles = ['ROLE_USER'];
        }
        if (array_key_exists('mobile_status', $loginStatus) && $loginStatus['mobile_status'] == 'N')
        {
          $roles[] = 'ROLE_NOT_ACTIVE_USER_NOT_ACTIVE_MOBILE';
        }
        
        return new WebserviceUser($username, $password, '', $roles);
      } catch (NotCorrectDataException $e)
      {
        $this->logger->error(print_r($e->getMessage(), true));
        throw new UsernameNotFoundException();
      }
      $this->logger->warning("new user");
      // Запросим дополнительные поля
      $fields = ['lastname','firstname','secname','region','city','regionguid','cityguid','birthdate', 'email', 'ismale'];
      $p2 = $participantApi->getById($participant->id,$fields);
      foreach ($fields as $field)
      {
        $participant->{$field} = $p2->{$field};
      }
      
      return new WebserviceUser($participant->getEmail(), $password, '', ['ROLE_USER'], $participant);
    }
    
    public function __construct(LoggerInterface $logger)
    {
      $this->logger = $logger;
    }
    
    public function refreshUser(UserInterface $user)
    {
      $this->logger->info("refreshUser");
      if (!$user instanceof WebserviceUser)
      {
        throw new UnsupportedUserException(
          sprintf('Instances of "%s" are not supported.', get_class($user))
        );
      }
      
      return $this->loadUserByUsername($user->getUsername());
    }
    
    public function supportsClass($class)
    {
      return WebserviceUser::class === $class;
    }
  }