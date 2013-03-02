<?php

namespace Deft\DataTables\Tests;

use Deft\DataTables\DataSource\DataSet;
use Deft\DataTables\Request\Request;
use Deft\DataTables\Server;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Server
     */
    protected $server;

    public function setUp()
    {
        $requestParserMock = $this->getMock('Deft\DataTables\Request\RequestParserInterface');
        $requestParserMock
            ->expects($this->once())
            ->method('parseRequest')
            ->will($this->returnValue(new Request()))
        ;

        $responseMock = $this->getMock('Deft\DataTables\Response\Response');
        $responseMock
            ->expects($this->once())
            ->method('createHttpResponse')
            ->will($this->returnValue(new HttpResponse()))
        ;
        $responseFactoryMock = $this->getMock('Deft\DataTables\Response\ResponseFactoryInterface');
        $responseFactoryMock
            ->expects($this->once())
            ->method('createResponse')
            ->will($this->returnValue($responseMock))
        ;

        $dataSourceMock = $this->getMock('Deft\DataTables\DataSource\DataSourceInterface');
        $dataSourceMock
            ->expects($this->once())
            ->method('createDataSet')
            ->will($this->returnValue(new DataSet()))
        ;

        $this->server = new Server(
            $requestParserMock,
            $responseFactoryMock,
            $dataSourceMock
        );
    }

    public function testHandleRequest()
    {
        $httpRequest = new HttpRequest();
        $httpResponse = $this->server->handleRequest($httpRequest);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $httpResponse);
    }
}
