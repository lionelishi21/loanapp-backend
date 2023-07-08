<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 08/09/2019
 * Time: 22:31
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\PaymentFrequency;
use App\SmartMicro\Repositories\Contracts\PaymentFrequencyInterface;

class PaymentFrequencyRepository extends BaseRepository implements PaymentFrequencyInterface
{

    protected $model;

    /**
     * PaymentFrequencyRepository constructor.
     * @param PaymentFrequency $model
     */
    function __construct(PaymentFrequency $model)
    {
        $this->model = $model;
    }

}