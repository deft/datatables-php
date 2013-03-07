<?php

namespace Deft\DataTables\DataSource;

use Deft\DataTables\DataSource\DataSet;
use Deft\DataTables\Request\Request;

interface DataSourceInterface
{
    /**
     * @param  Request $request
     * @return DataSet
     */
    public function createDataSet(Request $request);
}
