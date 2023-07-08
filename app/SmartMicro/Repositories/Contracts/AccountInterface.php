<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/08/2019
 * Time: 10:31
 */

namespace App\SmartMicro\Repositories\Contracts;

interface AccountInterface extends BaseInterface
{
    /**
     * @param $accountCode
     * @return mixed
     */
    function filterAccounts($accountCode);

    /**
     * @param $accountCode
     * @param $select
     * @param array $load
     * @return mixed
     */
    function listAccounts($accountCode, $select, $load = array());

    /**
     * @param $accountId
     * @return mixed
     */
    function fetchAccountStatement($accountId);

    /**
     * @param $accountId
     * @return mixed
     */
    function accountBalance($accountId);
}