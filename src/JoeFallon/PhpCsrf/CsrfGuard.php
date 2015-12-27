<?php
namespace JoeFallon\PhpCsrf;

use Exception;
use InvalidArgumentException;
use JoeFallon\PhpSession\Session;

class CsrfGuard
{
    /** @var string */
    protected $_formName;
    /** @var Session */
    protected $_session;

    /**
     * @param string  $formName
     * @param Session $session
     *
     * @throws InvalidArgumentException
     */
    public function __construct($formName, Session $session)
    {
        $formName = strval($formName);

        if(strlen($formName) == 0)
        {
            $msg = 'An empty form name is not allowed.';
            throw new InvalidArgumentException($msg);
        }

        $this->_formName = $formName;
        $this->_session = $session;
    }


    /**
     * @return string
     * @throws Exception
     */
    public function generateToken()
    {
        $isSecure = false;
        $token = (string)bin2hex(openssl_random_pseudo_bytes(32, $isSecure));

        if(!$isSecure)
        {
            throw new Exception("Random value algorithm was not secure.");
        }

        $session = $this->_session;
        $key = $this->_formName;
        $session->write($key, $token);

        return $token;
    }


    /**
     * @param string $token
     *
     * @throws Exception
     *
     * @return boolean
     */
    public function isValidToken($token)
    {
        $token = strval($token);

        if(strlen($token) == 0)
        {
            $msg = 'The token cannot be empty.';
            throw new Exception($msg);
        }

        $session = $this->_session;
        $key = $this->_formName;
        $sessionToken = $session->read($key);
        $session->unsetSessionValue($key);

        if(empty($sessionToken))
        {
            return false;
        }

        if($sessionToken === $token)
        {
            return true;
        }

        return false;
    }
}
