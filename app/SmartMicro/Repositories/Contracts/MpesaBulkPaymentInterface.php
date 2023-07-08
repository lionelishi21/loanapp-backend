<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 24/01/2020
 * Time: 09:22
 */

namespace App\SmartMicro\Repositories\Contracts;

interface MpesaBulkPaymentInterface extends BaseInterface
{
    public function customerCount();

    public function transactionValue();
}