<?php

namespace Deft\DataTables\Tests\DataSource;

use Deft\DataTables\DataSource\ArrayDataSource;
use Deft\DataTables\Request\Request;

class ArrayDataSourceTest extends \PHPUnit_Framework_TestCase
{
    protected $data = [
        ['col1' => 'foo', 'col2' => 'bar'],
        ['col1' => 'bar', 'col2' => 'foo'],
        ['col1' => 'foo', 'col2' => 'barfoo']
    ];

    /**
     * @var ArrayDataSource
     */
    protected $dataSource;

    public function setUp()
    {
        $this->dataSource = new ArrayDataSource($this->data);
    }

    public function testCreateDataSet_sort()
    {
        $request = $this->getBaseRequest();
        $request->columnSorts['col2'] = 'asc';

        $expectedData = [
            ['col1' => 'foo', 'col2' => 'bar'],
            ['col1' => 'foo', 'col2' => 'barfoo'],
            ['col1' => 'bar', 'col2' => 'foo']
        ];

        $dataSet = $this->dataSource->createDataSet($request);
        $this->assertEquals($expectedData, $dataSet->data);
    }

    public function testCreateDataSet_multiSort()
    {
        $request = $this->getBaseRequest();
        $request->columnSorts['col1'] = 'asc';
        $request->columnSorts['col2'] = 'desc';

        $expectedData = [
            ['col1' => 'bar', 'col2' => 'foo'],
            ['col1' => 'foo', 'col2' => 'barfoo'],
            ['col1' => 'foo', 'col2' => 'bar']
        ];

        $dataSet = $this->dataSource->createDataSet($request);
        $this->assertEquals($expectedData, $dataSet->data);
    }

    public function testCreateDataSet_filters()
    {
        $request = $this->getBaseRequest();
        $request->columnFilters['col1'] = 'foo';

        $expectedData = [
            ['col1' => 'foo', 'col2' => 'bar'],
            ['col1' => 'foo', 'col2' => 'barfoo']
        ];

        $dataSet = $this->dataSource->createDataSet($request);
        $this->assertEquals($expectedData, $dataSet->data);
    }

    /**
     * @return \Deft\DataTables\Request\Request
     */
    protected function getBaseRequest()
    {
        $request = new Request();
        $request->displayStart = 0;
        $request->displayLength = 10;

        return $request;
    }
}
