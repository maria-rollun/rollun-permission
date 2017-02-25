<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10.02.17
 * Time: 13:06
 */

namespace rollun\permission\Auth\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use rollun\permission\Auth\Adapter\AbstractWebAdapter;
use rollun\permission\Auth\AlreadyLogginException;
use rollun\permission\Auth\CredentialInvalidException;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Authentication\Result;
use Zend\Stratigility\MiddlewareInterface;

class LazyAuthenticationAction extends AbstractAuthenticationAction
{
    /**
     * Authentication user
     * @param Request $request
     * @param Response $response
     * @param null|callable $out
     * @return null|Response
     * @throws AlreadyLogginException
     * @throws CredentialInvalidException
     */
    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        if (!$this->authenticationService->hasIdentity()) {

            $this->adapter->setRequest($request);
            $this->adapter->setResponse($response);

            $result = $this->authenticationService->authenticate($this->adapter);

            if ($result->isValid()) {
                $identity = $result->getIdentity();
                $request = $request->withAttribute(static::KEY_IDENTITY, $identity);
            } else if ($result->getCode() === Result::FAILURE_CREDENTIAL_INVALID) {
                $request = $this->adapter->getRequest();
                $response = $this->adapter->getResponse();
            } else {
                throw new CredentialInvalidException("Auth credential error.");
            }
        } else {
            throw new AlreadyLogginException();
        }
        if (isset($out)) {
            return $out($request, $response);
        }
        return $response;
    }
}
