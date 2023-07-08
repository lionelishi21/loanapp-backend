<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 29/09/2019
 * Time: 14:31
 */

namespace App\Http\Resources;


use App\Models\GeneralSetting;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{

    /**
     * @param $date
     * @return mixed
     */
    function formatDateTime($date) {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @param $amount
     * @return string
     */
    public function formatMoney($amount) {
        return number_format($amount, $this->amountDecimal(), $this->amountDecimalSeparator(), $this->amountThousandSeparator());
    }

    /**
     * @param $date
     * @return false|string
     */
    public function formatDate($date){
        return $new_date_format = date($this->dateFormat(), strtotime($date));
    }


    /**
     * @return string
     */
    private function dateFormat(){
        $format = GeneralSetting::select('date_format')->first()->date_format;

        if(isset($format))
            return $format;
        return 'd-m-Y';
    }

    /**
     * @return string
     */
    private function amountThousandSeparator() {
        $separator = GeneralSetting::select('amount_thousand_separator')->first()->amount_thousand_separator;

        if(isset($separator))
            return $separator;
        return ',';
    }

    /**
     * @return string
     */
    private function amountDecimalSeparator() {
        $separator = GeneralSetting::select('amount_decimal_separator')->first()->amount_decimal_separator;

        if(isset($separator))
            return $separator;
        return '.';
    }

    /**
     * @return int
     */
    private function amountDecimal() {
        $separator = GeneralSetting::select('amount_decimal')->first()->amount_decimal;

        if(isset($separator))
            return (int)$separator;
        return 2;
    }

}