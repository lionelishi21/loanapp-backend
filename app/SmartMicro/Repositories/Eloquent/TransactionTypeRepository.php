<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 28/08/2019
 * Time: 14:33
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\TransactionType;
use App\SmartMicro\Repositories\Contracts\TransactionTypeInterface;

class TransactionTypeRepository extends BaseRepository implements TransactionTypeInterface
{

    protected $model;

    /**
     * PaymentRepository constructor.
     * @param TransactionType $model
     */
    function __construct(TransactionType $model)
    {
        $this->model = $model;
    }

}