<?php

class MailfireEmailTest extends PHPUnit_Framework_TestCase
{

    public function testShouldCheckActionCheck()
    {
        $email = 'someone@example.com';
        $predefinedResult = array('data' => 'ok');

        $request = $this->getMockBuilder('MailfireRequest')
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('create')
            ->with(MailfireEmail::CHECK_EMAIL_RESOURCE, array('email' => $email, 'sanitize' => true))
            ->will($this->returnValue($predefinedResult));

        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $mf = new Mailfire($clientId, $clientKey);
        $mf->errorHandler->setErrorMode(MailfireErrorHandler::MODE_EXCEPTION);
        $mf->request = $request;
        $result = $mf->email->check($email);
//        var_dump($result);
        $this->assertEquals($predefinedResult, $result);
    }
}
