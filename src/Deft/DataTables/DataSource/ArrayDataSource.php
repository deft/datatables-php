<?php

namespace Deft\DataTables\DataSource;

use Deft\DataTables\Request\Request;

class ArrayDataSource implements DataSourceInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $data;

    /**
     * Filtered and sorted data
     *
     * @var array
     */
    private $displayData;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function createDataSet(Request $request)
    {
        $displayData = $this->sortData(
            $request,
            $this->filterData($request, $this->data)
        );

        $dataSet = new DataSet();
        $dataSet->numberOfTotalRecords = count($this->data);
        $dataSet->numberOfFilteredRecords = count($displayData);
        $dataSet->data = array_values(
            array_slice(
                $displayData,
                $request->displayStart,
                $request->displayLength
            )
        );

        return $dataSet;
    }

    protected function filterData(Request $request, $data)
    {
        if (count($request->columnFilters) == 0) return $data;
        return array_filter(
            $data,
            function ($row) use ($request) {
                $filterResults = [];
                array_walk($request->columnFilters, function ($filter, $column) use ($row, &$filterResults) {
                    $filterResults[] = false !== strpos($row[$column], $filter);
                });

                return !in_array(false, $filterResults);
            }
        );
    }

    protected function sortData(Request $request, $data)
    {
        $sorts = $request->columnSorts;
        if (count($sorts) == 0) return $data;

        usort($data, function ($a, $b) use ($sorts) {
            foreach ($sorts as $column => $direction) {
                if ($a[$column] == $b[$column]) continue;
                $result = $a[$column] < $b[$column] ? -1 : 1;

                return $direction == 'asc' ? $result : $result * -1;
            }

            return 1;
        });

        return $data;
    }
}
