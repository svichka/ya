<?php
/**
 * Created by PhpStorm.
 * User: iampi
 * Date: 026, 26, 07, 2017
 * Time: 14:05
 */

namespace AppBundle\Controller;


class Base extends \Symfony\Bundle\FrameworkBundle\Controller\Controller
{
	protected function getParticipantIdOfFail()
	{
		$user = $this->getUser();

		$participant = $user->getParticipant();

		return $participant->id;
	}

	/**
	 * @return \AppBundle\Security\User\WebserviceUser
	 */
	public function getUser()
	{
		return parent::getUser();
	}
}
