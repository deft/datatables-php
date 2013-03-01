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

    public function testGetTotalNumberOfRecords()
    {
        $this->assertEquals(3, $this->dataSource->getTotalNumberOfRecords());
    }

    public function testGetNumberOfFilteredRecords()
    {
        $request = $this->getBaseRequest();
        $request->columnFilters['col1'] = 'foo';

        $this->dataSource->setRequest($request);
        $this->assertEquals(2, $this->dataSource->getNumberOfFilteredRecords($request));
    }

    public function testGetData()
    {
        $this->dataSource->setRequest($this->getBaseRequest());
        $this->assertEquals(
            $this->data,
            $this->dataSource->getDisplayData()
        );
    }

    public function testGetData_sort()
    {
        $request = $this->getBaseRequest();
        $request->columnSorts['col2'] = 'asc';

        $expectedData = [
            ['col1' => 'foo', 'col2' => 'bar'],
            ['col1' => 'foo', 'col2' => 'barfoo'],
            ['col1' => 'bar', 'col2' => 'foo']
        ];

        $this->dataSource->setRequest($request);
        $this->assertEquals($expectedData, $this->dataSource->getDisplayData());
    }

    public function testGetData_multiSort()
    {
        $request = $this->getBaseRequest();
        $request->columnSorts['col1'] = 'asc';
        $request->columnSorts['col2'] = 'desc';

        $expectedData = [
            ['col1' => 'bar', 'col2' => 'foo'],
            ['col1' => 'foo', 'col2' => 'barfoo'],
            ['col1' => 'foo', 'col2' => 'bar']
        ];

        $this->dataSource->setRequest($request);
        $this->assertEquals($expectedData, $this->dataSource->getDisplayData());
    }

    public function testGetData_filters()
    {
        $request = $this->getBaseRequest();
        $request->columnFilters['col1'] = 'foo';

        $expectedData = [
            ['col1' => 'foo', 'col2' => 'bar'],
            ['col1' => 'foo', 'col2' => 'barfoo']
        ];

        $this->dataSource->setRequest($request);
        $this->assertEquals($expectedData, $this->dataSource->getDisplayData());
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
