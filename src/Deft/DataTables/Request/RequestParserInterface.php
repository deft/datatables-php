<?php
namespace Deft\DataTables\Request;

use Symfony\Component\HttpFoundation\Request;

interface RequestParserInterface
{
    public function parseRequest(Request $request);
}
