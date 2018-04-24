<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Login;
use AppBundle\Entity\User;

use AppBundle\Exception\RException;
use AppBundle\Form\Type\Login\LoginForm;
use Dalee\PEPUWSClientBundle\Controller\ParticipantApiController;
use Dalee\PEPUWSClientBundle\Entity\Participant;
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
        $this->get('logger')->error($request->get('_username'));
        $this->get('logger')->error($request->get('g-recaptcha-response'));

        $authenticationUtils = $this->get('security.authentication_utils');
        $e = null;
        if (isset($_SESSION['error']))
        {
            $e = $_SESSION['error'];
        }
        if ($e == null)
        {
            $error = $authenticationUtils->getLastAuthenticationError();
        }
        else
        {
            unset($_SESSION['error']);
            session_commit();
            $error = new RException();
        }

        $lastUsername = $authenticationUtils->getLastUsername();
        $form = $this->createForm(LoginForm::class, new Login());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
        }
        else
        {
            return $this->render('AppBundle:Default:login.html.twig', [
                'last_username' => $lastUsername,
                'error'         => 'Ошибка заполнения каптчи',
                'code'          => '',
                'form'          => $form->createView(),
            ]);
        }
        $user = $this->getUser();
        if ($user)
        {
            $userRoles = $user->getRoles();
            if (in_array('ROLE_USER', $userRoles))
            {
                $this->addFlash('login', 'ok');

                $u = $this->getDoctrine()->getRepository('AppBundle:User')->find($user->getParticipant()->id);
                if ($u == null)
                {
                    $u = new User();
                }
                $u->setId($user->getParticipant()->id);
                $u->setAgree(1);
                $this->getDoctrine()->getManager()->merge($u);
                $this->getDoctrine()->getManager()->flush();
                if ($user->getParticipant()->is_new_in_project​)
                {
                    $this->addFlash('firstlogin', 'ok');
                }


                return $this->redirectToRoute('personal_page');
            }
            elseif (in_array('ROLE_NOT_ACTIVE_USER', $userRoles))
            {
                if (in_array('ROLE_NOT_ACTIVE_USER_NOT_ACTIVE_EMAIL', $userRoles))
                {
                    $error = ['messageKey' => 'Please, activate email', 'messageData' => []];
                    try
                    {
                        (new ParticipantApiController())->activationGenerate($user->getUsername());
                    }
                    catch (\Exception $e)
                    {

                    }
                }
                else
                {
                    $this->addFlash('login', 'ok');

                    $u = $this->getDoctrine()->getRepository('AppBundle:User')->find($user->getParticipant()->id);
                    if ($u == null)
                    {
                        $u = new User();
                    }
                    $u->setId($user->getParticipant()->id);
                    $u->setAgree(1);
                    $this->getDoctrine()->getManager()->merge($u);
                    $this->getDoctrine()->getManager()->flush();
                    if ($user->getParticipant()->is_new_in_project​)
                    {
                        $this->addFlash('firstlogin', 'ok');
                    }

                    return $this->redirectToRoute('personal_page');
                }
                $this->container->get('security.token_storage')->setToken(null);
            }
        }
//      }
        $restore = $request->get("code", false);


        return $this->render('AppBundle:Default:login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'code'          => $restore,
            'form'          => $form->createView(),
        ]);
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
                $this->container->get('security.token_storage')->setToken(null);
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
        $this->container->get('security.token_storage')->setToken(null);

        return $this->redirectToRoute('index_page');
    }
}
