<?php
namespace Deft\DataTables\Tests\Request;

use Deft\DataTables\Request\RequestParser;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class RequestParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequestParser
     */
    private $requestParser;

    public function setUp()
    {
        $this->requestParser = new RequestParser();
    }

    public function testParse_plain()
    {
        $dtRequest = $this->parseRequestFromParameters($this->getDefaultParameters());
        $this->assertEquals('1', $dtRequest->echo);
        $this->assertEquals(0, $dtRequest->displayStart);
        $this->assertEquals(10, $dtRequest->displayLength);
        $this->assertEquals([], $dtRequest->columnFilters);
        $this->assertEquals(['column_1' => 'asc'], $dtRequest->columnSorts);
    }

    public function testParse_sorting()
    {
        $parameters = array_merge(
            $this->getDefaultParameters(),
            ['iSortingCols' => 2, 'iSortCol_0' => 2, 'sSortDir_0' => 'desc', 'iSortCol_1' => 1, 'sSortDir_1' => 'asc']
        );

        $dtRequest = $this->parseRequestFromParameters($parameters);
        $this->assertEquals(
            ['column_3' => 'desc', 'column_2' => 'asc'],
            $dtRequest->columnSorts
        );
    }

    public function testParse_searching()
    {
        $parameters = array_merge(
            $this->getDefaultParameters(),
            ['sSearch' => 'foo', 'sSearch_0' => 'abc']
        );

        $dtRequest = $this->parseRequestFromParameters($parameters);
        $this->assertEquals('foo', $dtRequest->globalSearch);
        $this->assertEquals(
            ['column_1' => 'abc'],
            $dtRequest->columnFilters
        );
    }

    private function parseRequestFromParameters($parameters)
    {
        return $this->requestParser->parseRequest(new HttpRequest($parameters));
    }

    private function getDefaultParameters()
    {
        return [
            'sEcho' => '1',
            'iColumns' => 3,
            'iDisplayStart' => 0,
            'iDisplayLength' => 10,
            'mDataProp_0' => 'column_1',
            'mDataProp_1' => 'column_2',
            'mDataProp_2' => 'column_3',
            'sSearch' => '',
            'bRegex' => false,
            'bSearchable_0' => true,
            'bSearchable_1' => true,
            'bSearchable_2' => true,
            'sSearch_0' => '',
            'sSearch_1' => '',
            'sSearch_2' => '',
            'bRegex_0' => false,
            'bRegex_1' => false,
            'bRegex_2' => false,
            'iSortingCols' => 1,
            'iSortCol_0' => 0,
            'sSortDir_0' => 'asc',
            'bSortable_0' => false,
            'bSortable_1' => false,
            'bSortable_2' => false
        ];
    }
}
