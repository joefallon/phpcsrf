<?php
namespace JoeFallon\PhpCsrf;

use Exception;
use InvalidArgumentException;
use JoeFallon\PhpSession\Session;

/**
 * To use this class perform the following:
 *
 * 1.   Construct the class and pass in the name of the form and the
 *      session object.
 *
 * 2.   Call generateToken() and store the return value in a hidden form
 *      field called csrf.
 *
 * 3.   On post, pass the value of the csrf field into validateToken. If true
 *      is returned, then CSRF has not occurred.
 *
 * @author    Joseph Fallon <joseph.t.fallon@gmail.com>
 * @copyright Copyright 2014 Joseph Fallon (All rights reserved)
 * @license   MIT
 */
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
     * @throws \InvalidArgumentException
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
        $this->_session  = $session;
    }
    
    
    /**
     * @return string
     */
    public function generateToken()
    {
        $bytes = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
        $b64 = base64_encode($bytes);
        
        $token  = hash('sha1', $b64);
        $sess   = $this->_session;
        $key    = $this->_formName;
        
        $sess->write($key, $token);
        
        return $token;
    }


    /**
     * @param $token
     * @throws Exception
     *
     * @return boolean
     */
    public function validateToken($token)
    {
        $token = strval($token);
        
        if(strlen($token) == 0)
        {
            $msg = 'The token cannot be empty.';
            throw new Exception($msg);
        }
        
        $sess      = $this->_session;
        $key       = $this->_formName;
        $sessToken = $sess->read($key);
        $sess->unsetSessionValue($key);
        
        if($sessToken == false || strlen($sessToken) == 0)
        {
            return false;
        }
        
        if($sessToken === $token)
        {
            return true;
        }
        
        return false;
    }
}
