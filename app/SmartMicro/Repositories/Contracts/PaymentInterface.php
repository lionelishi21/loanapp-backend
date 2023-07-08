<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 16/12/2018
 * Time: 11:12
 */

namespace App\SmartMicro\Repositories\Contracts;

interface PaymentInterface extends BaseInterface {

    public function totalMpesaDeposits();
}