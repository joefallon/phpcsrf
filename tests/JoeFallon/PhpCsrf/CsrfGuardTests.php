<?php
namespace tests\JoeFallon\PhpCsrf;

use Exception;
use JoeFallon\KissTest\UnitTest;
use JoeFallon\PhpCsrf\CsrfGuard;
use JoeFallon\PhpSession\Session;

class CsrfGuardTests extends UnitTest
{
    public function setUp()
    {
        $session = new Session();
        $session->destroy();
    }

    public function test_empty_formName_throws_exception()
    {
        try
        {
            $session = new Session();
            new CsrfGuard('', $session);
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
        $session = new Session();
        $name = 'form_name';
        $csrf = new CsrfGuard($name, $session);
        $csrf->generateToken();

        $token = $session->read($name);
        $length = strlen($token);
        $session->unsetSessionValue('form_name');

        $this->assertFirstGreaterThanSecond($length, 0);
    }

    public function test_correct_token_is_written_to_session()
    {
        $session = new Session();
        $name = 'form_name';
        $csrf = new CsrfGuard($name, $session);
        $token = $csrf->generateToken();
        $out = $session->read($name);
        $session->unsetSessionValue('form_name');

        $this->assertEqual($out, $token);
    }

    public function test_token_length_is_correct()
    {
        $session = new Session();
        $name = 'form_name';
        $csrf = new CsrfGuard($name, $session);
        $token = $csrf->generateToken();

        $length = strlen($token);
        $session->unsetSessionValue('form_name');

        $this->assertEqual($length, 64);
    }

    public function test_isValidToken_returns_true_on_correct_token()
    {
        $session = new Session();
        $name = 'form_name';
        $csrf = new CsrfGuard($name, $session);
        $token = $csrf->generateToken();
        $result = $csrf->isValidToken($token);
        $session->unsetSessionValue('form_name');

        $this->assertTrue($result);
    }

    public function test_isValidToken_returns_false_in_incorrect_token()
    {
        $session = new Session();
        $name = 'form_name';
        $csrf = new CsrfGuard($name, $session);
        $csrf->generateToken();
        $result = $csrf->isValidToken('invalid token');
        $session->unsetSessionValue('form_name');

        $this->assertFalse($result);
    }

    public function test_isValidToken_throws_exception_on_empty_token()
    {
        $session = new Session();
        $name = 'test_name';
        $csrf = new CsrfGuard($name, $session);
        $csrf->generateToken();
        $session->unsetSessionValue('form_name');

        try
        {
            $csrf->isValidToken('');
        }
        catch(Exception $e)
        {
            $this->testPass();

            return;
        }

        $this->testFail();
    }

    public function test_isValidToken_unsets_session_after_validate()
    {
        $session = new Session();
        $name = 'form_name';
        $csrf = new CsrfGuard($name, $session);
        $csrf->generateToken();
        $csrf->isValidToken('invalid token');
        $result = $session->read($name);
        $session->unsetSessionValue('form_name');

        if($result != null)
        {
            $this->testFail();

            return;
        }

        $this->testPass();
    }

    public function test_isValidToken_false_on_missing_session_token()
    {
        $session = new Session();
        $name = 'form_name';
        $csrf = new CsrfGuard($name, $session);
        $token = $csrf->generateToken();
        $session->unsetSessionValue('form_name');

        $this->assertFalse($csrf->isValidToken($token));
    }
}
