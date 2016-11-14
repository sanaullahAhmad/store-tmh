<?php

if (!function_exists('datetime')) {

    /**
     * allows you to easily apply a runtime timezone value to all of your datetimes
     *
     * @param \Datetime $value
     * @param string    $format
     * @param string    $timezone
     * @return string
     */
    function datetime($value, $format = 'm/d/y', $timezone = null)
    {
        //timezone
        $timezone = ($timezone) ?: Config::get('timezone');

        //set timezone
        if ($timezone) {
            $value->setTimezone($timezone);
        }

        //format
        return $value->format($format);
    }
}



if (!function_exists('displayDate')) {

    /**
     * allows you to easily apply a runtime timezone value to all of your datetimes
     *
     * @param \Datetime $value
     * @param string    $format
     * @param string    $timezone
     * @return string
     */
    function displayDate($date, $timezone = null)
    {
        if ($date != '' && $date != '0000-00-00 00:00:00') {
            //timezone
            $timezone = ($timezone) ?: Config::get('timezone');

            //set timezone
            if ($timezone) {
                $date->setTimezone($timezone);
            }

            //format
            return $date->format('d F Y');
        }
    }
}

if (!function_exists('datetimeSpan')) {

    /**
     * allows you to easily
     *
     * @param \Datetime $start
     * @param \Datetime $end
     * @param string    $startFormat
     * @param string    $endFormat
     * @param string    $timezone
     * @return string
     */
    function datetimeSpan($start, $end, $startFormat, $endFormat, $timezone = null)
    {
        //timezone
        $timezone = ($timezone) ?: config('helpers.default_timezone');

        //set timezone
        if ($timezone) {
            $start->setTimezone($timezone);
            $end->setTimezone($timezone);
        }

        //format
        return $start->format($startFormat) . ' - ' . $end->format($endFormat);
    }
}


if (!function_exists('isDateTime')) {

    function isDateTime($datetime = false)
    {
        if($datetime) {
            if ($datetime != '' && $datetime != '0000-00-00 00:00:00')
            {
                return true;
            }
        }
        return false; 
    }
}


