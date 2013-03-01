<?php
namespace Deft\DataTables\Tests\Response;

use Deft\DataTables\DataSource\ArrayDataSource;
use Deft\DataTables\DataSource\DataSourceInterface;
use Deft\DataTables\Request\Request;
use Deft\DataTables\Response\ResponseFactory;

class ResponseFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var DataSourceInterface
     */
    private $dataSourceStub;

    private $data = [
        ['col1' => 'foo', 'col2' => 'bar'],
        ['col1' => 'bar', 'col2' => 'foo']
    ];

    public function setUp()
    {
        $this->responseFactory = new ResponseFactory();
        $this->dataSourceStub  = $this->getMock('Deft\DataTables\DataSource\DataSourceInterface');

        $this->dataSourceStub
            ->expects($this->any())
            ->method('getTotalNumberOfRecords')
            ->will($this->returnValue(10));

        $this->dataSourceStub
            ->expects($this->any())
            ->method('getNumberOfFilteredRecords')
            ->will($this->returnValue(2));

        $this->dataSourceStub
            ->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($this->data))
        ;
    }

    public function testCreateResponse()
    {
        $request = new Request();
        $request->echo = 2;
        $request->displayLength = 10;
        $request->displayStart = 0;

        $response = $this->responseFactory->createResponse($request, $this->dataSourceStub);

        $this->assertEquals(2, $response->echo);
        $this->assertEquals(10, $response->totalNumberOfRecords);
        $this->assertEquals(2, $response->numberOfFilteredRecords);
        $this->assertEquals($this->data, $response->data);
    }
}
