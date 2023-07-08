<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 16/12/2018
 * Time: 11:23
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\PaymentMethod;
use App\SmartMicro\Repositories\Contracts\PaymentMethodInterface;

class PaymentMethodRepository extends BaseRepository implements PaymentMethodInterface {

    protected $model;

    /**
     * PaymentMethodRepository constructor.
     * @param PaymentMethod $model
     */
    function __construct(PaymentMethod $model)
    {
        $this->model = $model;
    }

}