<?php

if (! function_exists('first')) {

    /**
     * Gets first element of array.
     *
     * @param array $array
     * @return mixed
     */
    function first(array $array)
    {
        return array_shift($array);
    }
}

if (! function_exists('crypto_number')) {

    /**
     * Format bitcoin number.
     *
     * @param $value
     * @return string
     */
    function crypto_number($value)
    {
        return number_format_trim_trailing_zero($value, 8, '.', '');
    }
}

if (! function_exists('number_format_trim_trailing_zero')) {

    /**
     * Formats a number and removes trailing zeros.
     *
     * @return string
     */
    function number_format_trim_trailing_zero()
    {
        return trim_trailing_zero(number_format(...func_get_args()));
    }
}

if (! function_exists('trim_trailing_zero')) {

    /**
     * Removes trailing zeros.
     *
     * @param $number
     * @return string
     */
    function trim_trailing_zero($number)
    {
        return strpos($number,'.') !== false
            ? rtrim(rtrim($number,'0'),'.')
            : $number;
    }
}
