<?php

namespace Deft\DataTables\DataTransformer;

class DateTimeTransformer implements DataTransformerInterface
{
    /**
     * The format to use when transforming the DateTime value
     * @var string
     */
    protected $format = 'Y-m-d H:i:s';

    public function __construct($format)
    {
        $this->format = $format;
    }

    /**
     * Transforms a value from the data source representation to the table representation
     *
     * @param  mixed
     * @return mixed
     */
    public function transform($value, &$row)
    {
        if (!($value instanceof \DateTime)) {
            return $value;
        }

        return $value->format($this->format);
    }
}
