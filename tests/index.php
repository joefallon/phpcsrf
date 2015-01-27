<?php
use JoeFallon\KissTest\UnitTest;

require('config/main.php');

new \tests\JoeFallon\PhpCsrf\CsrfGuardTests();

UnitTest::getAllUnitTestsSummary();
