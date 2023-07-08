<?php
/**
 * Created by PhpStorm.
 * Borrower: kevin
 * Date: 26/10/2018
 * Time: 12:11
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\Borrower;
use App\SmartMicro\Repositories\Contracts\BorrowerInterface;

class BorrowerRepository extends BaseRepository implements BorrowerInterface {

    protected $model;

    /**
     * BorrowerRepository constructor.
     * @param Borrower $model
     */
    function __construct(Borrower $model)
    {
        $this->model = $model;
    }

}