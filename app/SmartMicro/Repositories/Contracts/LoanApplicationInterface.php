<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 26/10/2018
 * Time: 12:26
 */

namespace App\SmartMicro\Repositories\Contracts;

interface LoanApplicationInterface extends BaseInterface {

    /**
     * @param array $load
     * @return mixed
     */
    public function getAllPending($load = array());

}