<?php

namespace Deft\DataTables\Tests\Response;

use Deft\DataTables\DataSource\DataSet;
use Deft\DataTables\Request\Request;
use Deft\DataTables\Response\ResponseFactory;

class ResponseFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    private $data = [
        ['col1' => 'foo', 'col2' => 'bar'],
        ['col1' => 'bar', 'col2' => 'foo']
    ];

    public function setUp()
    {
        $this->responseFactory = new ResponseFactory();
    }

    public function testCreateResponse()
    {
        $request = new Request();
        $request->echo = 1;
        $request->displayLength = 10;
        $request->displayStart = 0;

        $dataSet = new DataSet();
        $dataSet->numberOfTotalRecords = 10;
        $dataSet->numberOfFilteredRecords = 2;
        $dataSet->data = $this->data;

        $response = $this->responseFactory->createResponse($request, $dataSet);

        $this->assertEquals(1, $response->sEcho);
        $this->assertEquals(10, $response->iTotalRecords);
        $this->assertEquals(2, $response->iTotalDisplayRecords);
        $this->assertEquals($this->data, $response->aaData);
    }
}
