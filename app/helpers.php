<?php

use App\Models\GeneralSetting;

/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 12/02/2020
 * Time: 11:35
 */

/**
 * @param $amount
 * @return string
 */
function formatMoney($amount) {
    return number_format($amount, amountDecimal(), amountDecimalSeparator(), amountThousandSeparator());
}

/**
 * @param $date
 * @return false|string
 */
function formatDate($date){
    return $new_date_format = date(dateFormat(), strtotime($date));
}

/**
 * @return string
 */
function dateFormat(){
    $format = GeneralSetting::select('date_format')->first()->date_format;

    if(isset($format))
        return $format;
    return 'd-m-Y';
}

/**
 * @return string
 */
function amountThousandSeparator() {
    $separator = GeneralSetting::select('amount_thousand_separator')->first()->amount_thousand_separator;

    if(isset($separator))
        return $separator;
    return ',';
}

/**
 * @return string
 */
function amountDecimalSeparator() {
    $separator = GeneralSetting::select('amount_decimal_separator')->first()->amount_decimal_separator;

    if(isset($separator))
        return $separator;
    return '.';
}

/**
 * @return int
 */
function amountDecimal() {
    $separator = GeneralSetting::select('amount_decimal')->first()->amount_decimal;

    if(isset($separator))
        return (int)$separator;
    return 2;
}

/**
 *
 *  0724475357 -> 10 characters
 *  724475357 -> 9 characters
 * 254724475357 -> 12 characters
 *
 * @param $number
 * @return bool|string|string[]|null
 */
function mpesaNumber($number) {
    $number = preg_replace("/\s+/", "", $number);
    switch (strlen($number)){
        case '9' :{
            return '254'.$number;
            break;
        }
        case '10' :{
            return '254'.substr($number, 1);
            break;
        }
        case '12' :{
            return $number;
            break;
        }
        default:
            return null;
            break;
    }
}