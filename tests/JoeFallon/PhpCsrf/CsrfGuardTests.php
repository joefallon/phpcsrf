<?php
namespace tests\JoeFallon\PhpCsrf;

use Exception;
use JoeFallon\KissTest\UnitTest;
use JoeFallon\PhpCsrf\CsrfGuard;
use JoeFallon\PhpSession\Session;

/**
 * @author    Joseph Fallon <joseph.t.fallon@gmail.com>
 * @copyright Copyright 2014 Joseph Fallon (All rights reserved)
 * @license   MIT
 */
class CsrfGuardTests extends UnitTest
{
    public function setUp()
    {
        $sess = new Session();
        $sess->destroy();
    }
    
    public function test_empty_formName_throws_exception()
    {
        try
        {
            $sess = new Session();
            new CsrfGuard('', $sess);
        }
        catch(Exception $e)
        {
            $e = null;
            $this->testPass();
            return;
        }
        
        $this->testFail();
    }
    
    public function test_correct_key_is_written_to_session()
    {
        $sess = new Session();
        $name = 'test_name';
        $csrf = new CsrfGuard($name, $sess);
        $csrf->generateToken();
        
        $token  = $sess->read($name);
        $length = strlen($token);
        
        $this->assertFirstGreaterThanSecond($length, 0);
    }
    
    public function test_correct_token_is_written_to_session()
    {
        $sess = new Session();
        $name = 'test_name';
        $csrf = new CsrfGuard($name, $sess);
        $token = $csrf->generateToken('some_salt');
        $out   = $sess->read($name);
        
        $this->assertEqual($out, $token);
    }
    
    public function test_token_length_is_correct()
    {
        $sess = new Session();
        $name = 'test_name';
        $csrf = new CsrfGuard($name, $sess);
        $token = $csrf->generateToken('some_salt');
        
        $length = strlen($token);
        
        $this->assertEqual($length, 40);
    }
    
    public function test_validateToken_returns_true_on_correct_token()
    {
        $sess = new Session();
        $name = 'test_name';
        $csrf = new CsrfGuard($name, $sess);
        $token = $csrf->generateToken('some_salt');
        
        $result = $csrf->validateToken($token);
        
        $this->assertTrue($result);
    }
    
    public function test_validateToken_returns_false_in_incorrect_token()
    {
        $sess = new Session();
        $name = 'test_name';
        $csrf = new CsrfGuard($name, $sess);
        $token = $csrf->generateToken('some_salt');
        
        $result = $csrf->validateToken('invalid token');
        
        $this->assertFalse($result);
    }
    
    public function test_validateToken_throws_exception_on_empty_token()
    {
        $sess = new Session();
        $name = 'test_name';
        $csrf = new CsrfGuard($name, $sess);
        $token = $csrf->generateToken('some_salt');
        
        try
        {
            $result = $csrf->validateToken('');
        }
        catch(Exception $e)
        {
            $e = null;
            $this->testPass();
            return;
        }
        
        $this->testFail();
    }
    
    public function test_validate_token_unset_session_after_validate()
    {
        $sess  = new Session();
        $name  = 'test_name';
        $csrf  = new CsrfGuard($name, $sess);
        $token = $csrf->generateToken('some_salt');
        
        $result = $csrf->validateToken('invalid token');
        $result = $sess->read($name);
        
        if($result != null)
        {
            $this->testFail();
            return;
        }
        
        $this->testPass();
    }
}