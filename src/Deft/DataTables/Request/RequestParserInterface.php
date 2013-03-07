<?php

namespace Deft\DataTables\Request;

use Symfony\Component\HttpFoundation\Request as HttpRequest;

interface RequestParserInterface
{
    /**
     * @param  HttpRequest $request
     * @return Request
     */
    public function parseRequest(HttpRequest $request);
}
