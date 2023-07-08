<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/08/2019
 * Time: 10:33
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\AccountType;
use App\SmartMicro\Repositories\Contracts\AccountTypeInterface;

class AccountTypeRepository extends BaseRepository implements AccountTypeInterface
{

    protected $model;

    /**
     * AccountTypeRepository constructor.
     * @param AccountType $model
     */
    function __construct(AccountType $model)
    {
        $this->model = $model;
    }

}