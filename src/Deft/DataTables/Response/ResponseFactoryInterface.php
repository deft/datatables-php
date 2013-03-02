<?php

namespace Deft\DataTables\Response;

use Deft\DataTables\DataSource\DataSet;
use Deft\DataTables\Request\Request;

interface ResponseFactoryInterface
{
    /**
     * Creates a (model) Response from a Request and DataSet.
     *
     * @param  \Deft\DataTables\Request\Request    $request
     * @param  \Deft\DataTables\DataSource\DataSet $dataSet
     * @return Response
     */
    public function createResponse(Request $request, DataSet $dataSet);
}
