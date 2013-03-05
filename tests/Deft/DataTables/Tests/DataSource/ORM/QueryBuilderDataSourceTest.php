<?php

namespace Deft\DataTables\Tests\DataSource\ORM;

use Deft\DataTables\DataSource\ORM\QueryBuilderDataSource;
use Deft\DataTables\Request\Request;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Tests\Mocks\ConnectionMock;
use Doctrine\Tests\Mocks\DriverMock;
use Doctrine\Tests\Mocks\EntityManagerMock;

class QueryBuilderDataSourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueryBuilder
     */
    protected $qb;

    /**
     * @var QueryBuilderDataSource
     */
    protected $dataSource;

    /**
     * @var \StdClass
     */
    protected $testObject;

    public function setUp()
    {
        $em = EntityManagerMock::create(new ConnectionMock([], new DriverMock()));
        $this->qb = $em->createQueryBuilder()
            ->select('p.name, p.price, c.name')
            ->from('Product', 'p')
            ->join('p.category', 'c')
        ;

        $this->testObject = new \StdClass;
        $this->testObject->name = 'foobar';
        $this->testObject->price = 2.5;
        $this->testObject->category = new \StdClass;
        $this->testObject->category->name = 'foo';

        $this->dataSource = $this->createDataSourceMock([0 => 'p.name', 1 => 'p.price'], [$this->testObject]);
    }

    public function testCreateDataSet_simple()
    {
        $request = $this->getBaseRequest();
        $dataSet = $this->dataSource->createDataSet($request);

        $this->assertEquals(10, $dataSet->numberOfTotalRecords);
    }

    public function testCreateDataSet_pagination()
    {
        $request = $this->getBaseRequest();
        $request->displayStart = 100;
        $request->displayLength = 10;
        $dataSet = $this->dataSource->createDataSet($request);

        $this->assertEquals(100, $this->qb->getFirstResult());
        $this->assertEquals(10, $this->qb->getMaxResults());
    }

    public function testCreateDataSet_sort()
    {
        $request = $this->getBaseRequest();
        $request->columnSorts = [0 => 'desc', 1 => 'asc'];
        $dataSet = $this->dataSource->createDataSet($request);

        $orderBy = $this->qb->getDqlPart('orderBy');
        $this->assertEquals('p.name desc', $orderBy[0]->__toString());
        $this->assertEquals('p.price asc', $orderBy[1]->__toString());
    }

    public function testCreateDataSet_filters()
    {
        $request = $this->getBaseRequest();
        $request->columnFilters[0] = 'test';
        $dataSet = $this->dataSource->createDataSet($request);

        $where = $this->qb->getDQLPart('where')->getParts()[0]->getParts()[0];
        $this->assertEquals('p.name', $where->getLeftExpr());
        $this->assertEquals('LIKE', $where->getOperator());
        $this->assertEquals('%test%', $this->qb->getParameters()->first()->getValue());
    }

    public function testCreateDataSet_conflictingFilters()
    {
        $request = $this->getBaseRequest();
        $this->qb->andWhere('p.id = ?0')->setParameter(0, 'Test');
        $request->columnFilters[0] = 'test';
        $dataSet = $this->dataSource->createDataSet($request);

        $where = $this->qb->getDQLPart('where')->getParts();
        $this->assertCount(2, $where);
        $this->assertCount(2, $this->qb->getParameters());
    }

    public function testCreateDataSet_nullObject()
    {
        $object = new \StdClass;
        $object->name = 'Ghello';
        $object->category = null;
        $dataSource = $this->createDataSourceMock([0 => 'p.name', 1 => 'c.name'], [$object]);
        $dataSet = $dataSource->createDataSet($this->getBaseRequest());

        $this->assertEquals('Ghello', $dataSet->data[0][0]);
        $this->assertEmpty($dataSet->data[0][1]);
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

    protected function createDataSourceMock($columnMapping, $resultSet)
    {
        $dataSource = $this->getMock(
            'Deft\DataTables\DataSource\ORM\QueryBuilderDataSource',
            ['createPaginator'],
            [$this->qb, $columnMapping]
        );

        $paginatorMock = $this->getMock('Doctrine\ORM\Tools\Pagination\Paginator', [], [$this->qb]);
        $paginatorMock
            ->expects($this->any())
            ->method('count')
            ->will($this->returnValue(10))
        ;

        $paginatorMock
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($resultSet))
        ;

        $dataSource
            ->expects($this->any())
            ->method('createPaginator')
            ->will($this->returnValue($paginatorMock))
        ;

        return $dataSource;
    }
}
