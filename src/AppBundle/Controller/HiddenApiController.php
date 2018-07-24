<?php
/**
 * Created by PhpStorm.
 * User: svc
 * Date: 22.07.2018
 * Time: 23:47
 */

namespace AppBundle\Controller;
use Dalee\PEPUWSClientBundle\Controller\ClientApiController;
use Dalee\PEPUWSClientBundle\Exception\ApiFailedException;

class HiddenApiController extends ClientApiController
{
    private $logger;

    private function init()
    {
        /** @var \Symfony\Component\HttpKernel\Kernel $kernel */
        global $kernel;

        /** @var \Symfony\Component\DependencyInjection\Container $container */
        $container = $kernel->getContainer();
        $this->logger = $container->get('logger');
    }
    public function upsertEmail($email)
    {
        $this->init();
        $parameters = ["participant"=>['email'=>$email]];
        $this->callApi('/participants/upsert.json', 'post', $parameters);
        var_dump($this->getStatusCode());
        switch ($this->getStatusCode()) {
            case 200:
                $responseArray = $this->getResponse();
                $message = $responseArray['meta']['message'];
                $this->logger->alert($message);
                return '';
                break;

            case 500:
            case 400:
            default:
                $responseArray = $this->getResponse();
                if (is_array($responseArray) && array_key_exists('meta', $responseArray) && is_array($responseArray['meta']) && array_key_exists(
                        'message',
                        $responseArray['meta']
                    )) {
                    $message = $responseArray['meta']['message'];
                    $this->logger->alert($message);
                }
                return false;
                break;
            /*case 403:
            default:
                throw new ApiFailedException('Status code: '.$this->getStatusCode());
                break;*/
        }
        var_dump($this->getStatusCode());
        var_dump($this->getResponse());
    }
}