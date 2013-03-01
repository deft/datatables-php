<?php
namespace Deft\DataTables\Request;

use Symfony\Component\HttpFoundation\Request as HttpRequest;

interface RequestParserInterface
{
    /**
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return Request
     */
    public function parseRequest(HttpRequest $request);
}
