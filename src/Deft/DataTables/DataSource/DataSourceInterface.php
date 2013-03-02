<?php

namespace Deft\DataTables\DataSource;

use Deft\DataTables\Request\Request;

interface DataSourceInterface
{
    /**
     * @param \Deft\DataTables\Request\Request $request
     * @return
     */
    public function createDataSet(Request $request);
}
