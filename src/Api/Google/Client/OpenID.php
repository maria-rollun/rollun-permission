<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 30.01.17
 * Time: 15:55
 */

namespace rollun\permission\Api\Google\Client;

use Zend\Session\Container as SessionContainer;
use Psr\Http\Message\ServerRequestInterface as Request;


class OpenID extends ClientAbstract
{
    /** @var SessionContainer */
    protected $sessionContainer;

    /** @var string */
    protected $state;

    public function __construct(array $config = [], SessionContainer $sessionContainer, $code = null, $clientName = null)
    {
        $this->sessionContainer = $sessionContainer;
        parent::__construct($config, $code, $clientName);
    }

    public function saveCredential($accessToken)
    {
        $this->sessionContainer->accessToken = $accessToken;
    }

    public function getSavedCredential()
    {
        return $this->sessionContainer->accessToken ?: null;
    }

    public function getCodeResponse($state)
    {
        $this->sessionContainer->state = $state;
        return parent::getCodeResponse($state); // TODO: Change the autogenerated stub
    }



    public function getState()
    {
        return $this->state ?: null;
    }

    public function initByRequest(Request $request)
    {
        $query = $request->getQueryParams();
        if (isset($query['code'])) {
            $this->setCode($query['code']);
        }
        if (isset($query['state'])) {
            $this->state = $query['state'];
        }
    }
}
