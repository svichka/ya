<?php
  
  namespace AppBundle\Controller;
  
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\JsonResponse;
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
          $this->addFlash('login', 'ok');
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
     * @Route("/login_json/", name="login_json")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return JsonResponse
     */
    public function loginJsonAction(Request $request)
    {
    
      $authenticationUtils = $this->get('security.authentication_utils');
      $error = $authenticationUtils->getLastAuthenticationError();
      $lastUsername = $authenticationUtils->getLastUsername();
      $log = $this->get('logger');
    
      $user = $this->getUser();
      if ($user)
      {
        $log->error("user");
        $userRoles = $user->getRoles();
      
        if (in_array('ROLE_USER', $userRoles))
        {
          return JsonResponse::create(
            [
              'status'        => 200,
              'last_username' => $lastUsername,
              'error'         => $error,
            ]);
        }
        elseif (in_array('ROLE_NOT_ACTIVE_USER', $userRoles))
        {
          if (in_array('ROLE_NOT_ACTIVE_USER_NOT_ACTIVE_MOBILE', $userRoles))
          {
            $error = ['messageKey' => "Телефон не активирован", 'messageData' => []];
            if ($user->getParticipant() !== null)
            {
              return JsonResponse::create(
                [
                  'status'        => 200,
                  'last_username' => $lastUsername,
                  'error'         => $error,
                ]);
            }
          }
          if (in_array('ROLE_NOT_ACTIVE_USER_NOT_ACTIVE_EMAIL', $userRoles))
          {
            $url = $this->generateUrl('activation_request_page', ['login' => $user->getUsername()]);
            $error = ['messageKey' => "Емейл не активирован, <a href='$url' style='display: block;font-family: IntroBookItalic, sans-serif;color: #FFF;text-decoration: underline;font-size: 14px;'>запросить ссылку активации</a>", 'messageData' => []];
          }
          $this->container->get('security.context')->setToken(null);
        }
      }
      else
      {
        $log->error("not user");
        $error = ['messageKey' => 'Логин или пароль не верны', 'messageData' => []];
      }
    
      return JsonResponse::create(
        [
          'status'        => 400,
          'last_username' => $lastUsername,
          'error'         => $error,
        ]);
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
