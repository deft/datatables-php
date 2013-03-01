<?php

namespace Deft\DataTables\DataSource;

use Deft\DataTables\Request\Request;

interface DataSourceInterface
{
    public function getTotalNumberOfRecords();
    public function getNumberOfFilteredRecords(Request $request);
    public function getData(Request $request);
}
