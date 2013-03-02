<?php

namespace Deft\DataTables\Request;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class RequestParser implements RequestParserInterface
{
    /**
     * @param  HttpRequest $httpRequest
     * @return Request
     */
    public function parseRequest(HttpRequest $httpRequest)
    {
        $query = $httpRequest->query;

        $request = new Request();

        $request->echo = $query->get('sEcho');
        $request->displayStart = (int) $query->get('iDisplayStart');
        $request->displayLength = (int) $query->get('iDisplayLength');
        $request->globalSearch = $query->get('sSearch');

        $columnMapping = $this->createColumnMapping($query);

        foreach ($columnMapping as $i => $column) {
            $search = $query->get("sSearch_{$i}");
            if ($search) $request->columnFilters[$column] = $search;
        }

        for ($i = 0; $i < $query->get('iSortingCols'); $i++) {
            $columnName = $columnMapping[$query->get("iSortCol_{$i}")];
            $request->columnSorts[$columnName] = $query->get("sSortDir_{$i}");
        }

        return $request;
    }

    protected function createColumnMapping(ParameterBag $query)
    {
        $mapping = [];

        for ($i = 0; $i < $query->get('iColumns'); $i++) {
            $mapping[$i] = $query->get("mDataProp_{$i}");
        }

        return $mapping;
    }
}
