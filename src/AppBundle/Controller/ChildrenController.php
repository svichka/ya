<?php

namespace AppBundle\Controller;

use Dalee\PEPUWSClientBundle\Exception\ApiFailedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ChildrenController extends Base
{
	/**
	 * @Route("/children_list/", name="children_list")
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function listAction(Request $request)
	{
		$user = $this->getUser();

		$participant = $user->getParticipant();

		$api = new \Dalee\PEPUWSClientBundle\Controller\ChildrenApiController();

		try {

			$children = $api->getList(
				$participant->id
			);

		} catch (ApiFailedException $e) {
			$children = [];
		}


		foreach ($children as $k => $child) {
			$children[$k]['__serialized'] = $this->serialize($child);
		}

		return $this->render('AppBundle:Default:children_list.html.twig', [
			'children' => $children
		]);
	}

	protected function serialize($child)
	{
		return base64_encode(serialize($child));
	}

	/**
	 * @Route("/children_add", name="children_add")
	 */
	public function addAction(Request $request)
	{
		$api = new \Dalee\PEPUWSClientBundle\Controller\ChildrenApiController();

		$builder = $api->buildForm(
			$this->createFormBuilder()
		);

		$form = $builder->getForm();

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			try {

				$formData = $form->getData();

				if ($formData['birth_date'] instanceof \DateTime) {
					$formData['birth_date'] = $formData['birth_date']->format("d.m.Y");
				}

				$api->add(
					$this->getParticipantIdOfFail(),
					$formData
				);

				return $this->redirect('/children_list');

			} catch (\Dalee\PEPUWSClientBundle\Exception\NotCorrectDataException $e) {

			}
		}


		return $this->render('AppBundle:Default:children_form.html.twig', [
			'form' => $form->createView()
		]);
	}


	/**
	 * @Route("/children_delete/{child_id}", name="children_delete")
	 * @param $child_id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteAction($child_id)
	{
		try {

			$api = new \Dalee\PEPUWSClientBundle\Controller\ChildrenApiController();

			$api->delete($this->getParticipantIdOfFail(), $child_id);

			return $this->redirect('/children_list');

		} catch (\Dalee\PEPUWSClientBundle\Exception\NotCorrectDataException $e) {

		}

		return $this->redirect('/children_list');
	}

	/**
	 * @Route("/children_edit/{child_id}", name="children_edit")
	 */
	public function editAction($child_id, Request $request)
	{
		$api = new \Dalee\PEPUWSClientBundle\Controller\ChildrenApiController();

		$builder = $api->buildForm(
			$this->createFormBuilder()
		);

		$form = $builder->getForm();

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			try {

				$formData = $form->getData();

				if ($formData['birth_date'] instanceof \DateTime) {
					$formData['birth_date'] = $formData['birth_date']->format("d.m.Y");
				}

				$api->edit(
					$this->getParticipantIdOfFail(),
					$child_id,
					$formData
				);

				return $this->redirect('/children_list');

			} catch (\Dalee\PEPUWSClientBundle\Exception\NotCorrectDataException $e) {

			}
		} else if (!empty($request->get('__serialized'))) {

			$serializedData = $request->get('__serialized');

			$unSerializedData = $this->unserialize($serializedData);

			$formData = $unSerializedData;

			if (empty($formData['birth_date'])) {
				unset($formData['birth_date']);
			} else {
				$formData['birth_date'] = \DateTime::createFromFormat("d-m-Y", $formData['birth_date']);
			}

			$form->setData($formData);
		}


		return $this->render('AppBundle:Default:children_form.html.twig', [
			'form' => $form->createView()
		]);
	}

	protected function unserialize($child)
	{
		return unserialize(base64_decode($child));
	}
}
