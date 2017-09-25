<?php

namespace AppBundle\Controller;

use Dalee\PEPUWSClientBundle\Entity\Participant;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ReceiptController extends Base
{
	/**
	 * @Route("/receipt_widget/", name="receipt_widget")
	 */
	public function indexAction(Request $request)
	{
		$apiKey = $this->container->getParameter('apm.key');

		/** @var Participant $participant */
		$participant = $this->getUser()->getParticipant();

		return $this->render('AppBundle:Default:widget.html.twig', [
			'apiKey' => $apiKey,
			'userUuid' => $participant->guid
		]);
	}

	/**
	 * @Route("/receipt_list/", name="receipt_list")
	 */
	public function listAction(Request $request)
	{
		/** @var Participant $participant */
		$participant = $this->getUser()->getParticipant();

		$api = new \Dalee\PEPUWSClientBundle\Controller\ReceiptApiController;

		$receipts = $api->getParticipantReceipts($participant->id);

		return $this->render('AppBundle:Default:receipt_list.html.twig', [
			'receipts' => $receipts
		]);
	}
}
