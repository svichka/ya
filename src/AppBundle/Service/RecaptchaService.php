<?php

namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use ReCaptcha\ReCaptcha;

class RecaptchaService
{
	protected $isActive = false;
	protected $secretKey;
	protected $publicKey;
	protected $lastError = '';

	public function __construct(Container $container)
	{
		$isRecaptchaPublicKeyExist = $container->hasParameter('recaptcha.public_key');
		if ($isRecaptchaPublicKeyExist) {
			$this->publicKey = $container->getParameter('recaptcha.public_key');
			if (!$this->publicKey) {
				$isRecaptchaPublicKeyExist = false;
			}
		}
		$isRecaptchaSecretKeyExist = $container->hasParameter('recaptcha.secret_key');
		if ($isRecaptchaSecretKeyExist) {
			$this->secretKey = $container->getParameter('recaptcha.secret_key');
			if (!$this->secretKey) {
				$isRecaptchaSecretKeyExist = false;
			}
		}
		$this->isActive = $isRecaptchaSecretKeyExist && $isRecaptchaPublicKeyExist;
	}

	public function isActive()
	{
		return $this->isActive;
	}

	public function getPublicKey()
	{
		return $this->publicKey;
	}

	public function getSecretKey()
	{
		return $this->secretKey;
	}
	
	public function setLastError($value)
	{
		$this->lastError = $value;
		return $this;
	}

	public function getLastError()
	{
		return $this->lastError;
	}

	public function isSuccess($request)
	{
		if ($this->isActive()) {
			$recaptcha = new ReCaptcha($this->getSecretKey());
			$response = $recaptcha->verify($request->request->get('g-recaptcha-response'), $request->getClientIp());

			if (!$response->isSuccess()) {
				$this->setLastError("The reCAPTCHA wasn't entered correctly. Go back and try it again.");
				return false;
			}
		}
		return true;
	}
}