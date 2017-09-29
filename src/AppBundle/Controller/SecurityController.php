<?php
  
  namespace AppBundle\Controller;
  
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Request;
  
  class SecurityController extends Base
  {
    /**
     * @Route("/login/", name="login")
     */
    public function loginAction(Request $request)
    {
      $authenticationUtils = $this->get('security.authentication_utils');
      $error = $authenticationUtils->getLastAuthenticationError();
      $lastUsername = $authenticationUtils->getLastUsername();
      
      $user = $this->getUser();
      if ($user) {
        $userRoles = $user->getRoles();
        if (in_array('ROLE_USER', $userRoles)) {
          return $this->redirectToRoute('personal_page');
        } elseif (in_array('ROLE_NOT_ACTIVE_USER', $userRoles)) {
          if (in_array('ROLE_NOT_ACTIVE_USER_NOT_ACTIVE_MOBILE', $userRoles)) {
            $error = ['messageKey' => 'Please, activate mobile', 'messageData' => []];
          }
          if (in_array('ROLE_NOT_ACTIVE_USER_NOT_ACTIVE_EMAIL', $userRoles)) {
            $error = ['messageKey' => 'Please, activate email', 'messageData' => []];
          }
          $this->container->get('security.context')->setToken(null);
        }
      }
      
      return $this->render('AppBundle:Default:login.html.twig', array(
        'last_username' => $lastUsername,
        'error' => $error,
      ));
    }
    
    /**
     * @Route("/logout/", name="logout")
     */
    public function logoutAction(Request $request)
    {
      $this->container->get('security.context')->setToken(null);
      return $this->redirectToRoute('index_page');
    }
  }
