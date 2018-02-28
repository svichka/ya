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
      $this->checkAccess();
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
      $this->checkAccess();
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
      $this->checkAccess();
      
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
      $this->checkAccess();
      $lAPI = new PromoLotteryApiController();
      /**
       * {
       *  "is_runnable": true,
       *  "id": 2712,
       *  "prize": {
       *    "remaining_amount": 84,
       *    "id": 551,
       *    "title": "Сертификат Лена Ленина",
       *    "value": "3000.00",
       *    "group": null,
       *    "is_active": true,
       *    "crm_slug": "2492828",
       *    "slug": "certificate_lenina",
       *    "promopoints_value": "0.00",
       *    "limit_total": 84,
       *    "spent_total": 0,
       *    "balance_date": "2018-01-22T16:16:44+0300",
       *    "has_coupon_code": true
       *  },
       *  "start_time": "2018-02-21T13:00:01+0300",
       *  "end_time": "2018-02-21T13:30:01+0300",
       *  "winners_count": 6,
       *  "is_ready": false,
       *  "is_done": false,
       *  "ticket_price": 1,
       *  "ticket_multiuse": false,
       *  "formula": null,
       *  "is_auto_run": false,
       *  "run_time": "2018-02-21T13:31:00+0300",
       *  "auto_run_order": 3,
       *  "thread": "indep_220259"
       * },
       */
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
      $this->checkAccess();
      $lAPI = new PromoLotteryApiController();
      
      $ret = $lAPI->runLottery($promo, $id);
      
      return $this->render('AppBundle:Admin:lottery_run.html.twig', [
        'ret' => print_r($ret, true),
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
      $this->checkAccess();
      $lAPI = new PromoLotteryApiController();
      
      $ret = $lAPI->commitLottery($promo, $id);
      
      return $this->render('AppBundle:Admin:lottery_commit.html.twig', [
        'ret' => print_r($ret, true),
      ]);
    }
    
    private function checkAccess()
    {
      if ($this->getUser() == null)
      {
        throw new HttpException(403, "Доступ запрещён");
      }
//      if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles()))
//      {
//        throw new HttpException(403, "Доступ запрещён");
//      }
    }
  }