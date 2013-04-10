<?php

namespace Deft\DataTables\DataTransformer;

interface DataTransformerInterface
{
    /**
     * Transforms a value from the data source representation to the table representation
     *
     * @param  mixed
     * @param  mixed[]
     * @return mixed
     */
    public function transform($value, &$row);
}
