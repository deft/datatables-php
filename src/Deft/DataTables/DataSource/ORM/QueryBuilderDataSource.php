<?php

namespace Deft\DataTables\DataSource\ORM;

use Deft\DataTables\DataSource\DataSet;
use Deft\DataTables\DataSource\DataSourceInterface;
use Deft\DataTables\Request\Request;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class QueryBuilderDataSource implements DataSourceInterface
{
    /**
     * @var QueryBuilder
     */
    protected $qb;

    /**
     * Maps datatables columns to query columns
     *
     * @var array
     */
    protected $columnMapping;

    public function __construct(QueryBuilder $qb, array $columnMapping)
    {
        $this->qb = $qb;
        $this->columnMapping = $columnMapping;
    }

    /**
     * @param \Deft\DataTables\Request\Request $request
     * @return
     */
    public function createDataSet(Request $request)
    {
        $dataSet = new DataSet();

        $paginatorTotal = $this->createPaginator();
        $dataSet->numberOfTotalRecords = $paginatorTotal->count();

        // Add filters
        $where = [];
        $params = [];
        foreach ($request->columnFilters as $column => $filter) {
            $params[count($where)] = "%$filter%";
            $where[] = $this->qb->expr()->like($this->columnMapping[$column], '?' . count($where));
        }
        if (count($where) > 0) {
            $this->qb->where(call_user_func_array([$this->qb->expr(), 'andX'], $where));
            $this->qb->setParameters($params);
        }

        // Add sorting
        foreach ($request->columnSorts as $column => $direction) {
            $this->qb->addOrderBy($this->columnMapping[$column], $direction);
        }

        $this->qb->setFirstResult($request->displayStart);
        $this->qb->setMaxResults($request->displayLength);

        $paginatorFiltered = $this->createPaginator();
        $dataSet->numberOfFilteredRecords = $paginatorFiltered->count();
        $dataSet->data = [];

        $propertyPathMappingFactory = new PropertyPathMappingFactory();
        $propertyPathMapping = $propertyPathMappingFactory->createPropertyPathMapping($this->qb, $this->columnMapping);

        foreach ($paginatorFiltered->getIterator() as $item) {
            $dataSet->data[] = $this->buildRow($item, $propertyPathMapping);
        }

        return $dataSet;
    }

    protected function buildRow($item, $propertyPathMapping)
    {
        $row = [];
        foreach ($this->columnMapping as $column => $field) {
            try {
                $row[$column] = PropertyAccess::getPropertyAccessor()->getValue($item, $propertyPathMapping[$column]);
            } catch (UnexpectedTypeException $e) {
                $row[$column] = null;
            }
        }

        return $row;
    }

    public function createPaginator()
    {
        return new Paginator($this->qb);
    }
}
