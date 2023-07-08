<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/08/2019
 * Time: 10:33
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\AccountStatus;
use App\SmartMicro\Repositories\Contracts\AccountStatusInterface;

class AccountStatusRepository extends BaseRepository implements AccountStatusInterface
{

    protected $model;

    /**
     * AccountStatusRepository constructor.
     * @param AccountStatus $model
     */
    function __construct(AccountStatus $model)
    {
        $this->model = $model;
    }

}