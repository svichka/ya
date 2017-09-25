<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Dalee\PEPUWSClientBundle\Controller\ParticipantApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PrizeController extends Base
{
	/**
	 * @Route("/prize_list/", name="prize_list_page")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function prizeListAction(\Symfony\Component\HttpFoundation\Request $request)
	{
		$user = $this->getUser();
		$participant = $user->getParticipant();

		$api = new ParticipantApiController();


		try {
			$prizes = $api->getPrizes(
				$participant->id
			);

		} catch (\Dalee\PEPUWSClientBundle\Exception\Base $e) {
			$prizes = [];
		}


		return $this->render('AppBundle:Default:prize_list.html.twig', [
			'prizes' => $prizes,
		]);
	}
}
