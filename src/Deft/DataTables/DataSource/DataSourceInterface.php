<?php

namespace Deft\DataTables\DataSource;

use Deft\DataTables\Request\Request;

interface DataSourceInterface
{
    public function setRequest(Request $request);
    public function getTotalNumberOfRecords();
    public function getNumberOfFilteredRecords();
    public function getDisplayData();
}
