<?php

namespace Deft\DataTables\Tests\Response;

use Deft\DataTables\Response\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateHttpResponse()
    {
        $response = new Response();
        $response->sEcho = 1;
        $response->iTotalRecords = 100;
        $response->iTotalDisplayRecords = 1;
        $response->aaData = [['a' => 'b', 'c' => 'd']];

        $httpResponse = $response->createHttpResponse();
        $this->assertEquals(200, $httpResponse->getStatusCode());
        $this->assertEquals('application/json', $httpResponse->headers->get('content-type'));
        $this->assertEquals(
            '{"sEcho":1,"iTotalRecords":100,"iTotalDisplayRecords":1,"aaData":[{"a":"b","c":"d"}]}',
            $httpResponse->getContent()
        );
    }
}
