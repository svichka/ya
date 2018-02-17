<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 17.02.18
   * Time: 14:15
   */
  
  namespace AppBundle\Controller;
  
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpKernel\Exception\HttpException;
  
  class AdminController extends Base
  {
    /**
     * @Route("/admin/codes_all", name="admin_codes_all")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws HttpException
     */
    public function listAction(Request $request)
    {
      if ($this->getUser() == null)
      {
        throw new HttpException(403, "Доступ запрещён");
      }
      if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles()))
      {
        throw new HttpException(403, "Доступ запрещён");
      }
      $codes = $this->getDoctrine()->getRepository('AppBundle:CodeHistory')->findAll();
      $ret = [];
      foreach ($codes as $code)
      {
        $ret[] = [
          'id'        => $code->getId(),
          'user'      => $code->getUser(),
          'activated' => $code->getActivated() != null ? $code->getActivated()->format("Y-m-d H:i:s") : "",
          'code'      => $code->getCode(),
          'status'    => $code->getStatus(),
        ];
      }
      
      return $this->render('AppBundle:Admin:list.html.twig', [
        'codes' => $ret,
      ]);
    }
    
    /**
     * @Route("/admin/code_check", name="admin_codes_check")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws HttpException
     */
    public function checkCodeAction(Request $request)
    {
      if ($this->getUser() == null)
      {
        throw new HttpException(403, "Доступ запрещён");
      }
      if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles()))
      {
        throw new HttpException(403, "Доступ запрещён");
      }
      $code = null;
      $history = null;
      $search = $request->request->get('search');
      if ($search === null)
      {
        return $this->render('AppBundle:Admin:code.html.twig', [
          'code'    => $code,
          'history' => $history,
        ]);
      }
      $codes = $this->getDoctrine()->getRepository('AppBundle:CodeHistory')->findBy(['code' => $search]);
      
      foreach ($codes as $code)
      {
        $history[] = [
          'id'        => $code->getId(),
          'user'      => $code->getUser(),
          'activated' => $code->getActivated() != null ? $code->getActivated()->format("Y-m-d H:i:s") : "",
          'code'      => $code->getCode(),
          'status'    => $code->getStatus(),
        ];
      }
      $code = $this->getDoctrine()->getRepository('AppBundle:Code')->findOneBy(['code' => $search]);
      
      return $this->render('AppBundle:Admin:code.html.twig', [
        'code'    => $code,
        'history' => $history,
      ]);
    }
  }