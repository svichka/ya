<?php
  /**
   * Created by PhpStorm.
   * User: wrewolf
   * Date: 17.02.18
   * Time: 14:15
   */
  
  namespace AppBundle\Controller;
  
  use Dalee\PEPUWSClientBundle\Controller\PromoLotteryApiController;
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
    
    /**
     * @Route("/admin/code_lotteries", name="admin_all_lotteries")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws HttpException
     */
    public function allLotteriesAction(Request $request)
    {
      if ($this->getUser() == null)
      {
        throw new HttpException(403, "Доступ запрещён");
      }
      if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles()))
      {
        throw new HttpException(403, "Доступ запрещён");
      }
      
      return $this->render('AppBundle:Admin:all_lotteries.html.twig', [
      
      ]);
    }
    
    /**
     * @Route("/admin/code_lotteries/{promo}", name="admin_lotteries_check")
     * @param String $promo
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function checkLotteriesAction(String $promo)
    {
      if ($this->getUser() == null)
      {
        throw new HttpException(403, "Доступ запрещён");
      }
      if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles()))
      {
        throw new HttpException(403, "Доступ запрещён");
      }
      $lAPI = new PromoLotteryApiController();
      
      $lotteries = $lAPI->getLotteries($promo);
      $tmp = [];
      foreach ($lotteries as $lottery)
      {
        $tmp[] = [
          'id'          => $lottery['id'],
          'promo'       => $promo,
          'is_done'     => $lottery['is_done'],
          'is_ready'    => $lottery['is_ready'],
          'is_runnable' => $lottery['is_runnable'],
          'start_time'  => (new \DateTime($lottery['start_time']))->format("Y-m-d H:i:s"),
          'end_time'    => (new \DateTime($lottery['end_time']))->format("Y-m-d H:i:s"),
          'run_time'    => (new \DateTime($lottery['run_time']))->format("Y-m-d H:i:s"),
        ];
      }
      
      return $this->render('AppBundle:Admin:lotteries.html.twig', [
        'lotteries' => $tmp,
        'tmp'       => $lotteries,
      ]);
    }
    
    /**
     * @Route("/admin/code_lottery_run/{promo}/{id}", name="admin_lottery_run")
     * @param String  $promo
     * @param integer $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function runLotteryAction(String $promo, $id)
    {
      if ($this->getUser() == null)
      {
        throw new HttpException(403, "Доступ запрещён");
      }
      if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles()))
      {
        throw new HttpException(403, "Доступ запрещён");
      }
      $lAPI = new PromoLotteryApiController();
      
      $ret = $lAPI->runLottery($promo, $id);
      
      return $this->render('AppBundle:Admin:lottery_run.html.twig', [
        'ret' => $ret,
      ]);
    }
    
    /**
     * @Route("/admin/code_lottery_commit/{promo}/{id}", name="admin_lottery_commit")
     * @param String  $promo
     * @param integer $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function commitLotteryAction(String $promo, $id)
    {
      if ($this->getUser() == null)
      {
        throw new HttpException(403, "Доступ запрещён");
      }
      if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles()))
      {
        throw new HttpException(403, "Доступ запрещён");
      }
      $lAPI = new PromoLotteryApiController();
      
      $ret = $lAPI->runLottery($promo, $id);
      
      return $this->render('AppBundle:Admin:lottery_commit.html.twig', [
        'ret' => $ret,
      ]);
    }
  }