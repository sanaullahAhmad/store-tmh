<?php


if (!function_exists('moneyForField')) {

    /**
     * money
     *
     * @return string
     */


    function moneyForField($money)
    {
        if ($money != '' && $money != '0' && $money != '0.00')
            {
            $fmt = new NumberFormatter('de_DE', NumberFormatter::DECIMAL);
            $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);
            $format_money = $fmt->format($money);

            if (intl_is_failure($fmt->getErrorCode())) {
                return report_error("Formatter error");
            }

            return $format_money;
        }
    }
}



if (!function_exists('money')) {

    /**
     * money
     *
     * @return string
     */
    function money($money)
    { 
            $fmt = new NumberFormatter('de_DE', NumberFormatter::DECIMAL);
            $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);
            $money = $fmt->format($money);
            $symbol = $fmt->getSymbol(NumberFormatter::CURRENCY_SYMBOL);

            $money = $symbol . $money;

            if (intl_is_failure($fmt->getErrorCode())) {
                return report_error("Formatter error");
            }


            return $money;

    }
}

