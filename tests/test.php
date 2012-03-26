<?php
require_once "PHPUnit/Autoload.php";
require_once "User.php";

class tests extends PHPUnit_Framework_TestCase
{
    public function testTalk() {
        $user = new User();

        $expected = "Hello world!";
        $actual = $user->talk();
        $this->assertEquals($expected, $actual);
    }

}