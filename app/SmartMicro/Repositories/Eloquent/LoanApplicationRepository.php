<?php
/**
 * Created by PhpStorm.
 * LoanApplication: kevin
 * Date: 26/10/2018
 * Time: 12:26
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\LoanApplication;
use App\SmartMicro\Repositories\Contracts\LoanApplicationInterface;

class LoanApplicationRepository extends BaseRepository implements LoanApplicationInterface {

    protected $model;

    /**
     * LoanApplicationRepository constructor.
     * @param LoanApplication $model
     */
    function __construct(LoanApplication $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $load
     * @return mixed
     */
    public function getAllPending($load = array())
    {
        $whereField = 'reviewed_on';
        $whereValue = null;
        $searchFilter = (string)(request()->query('filter')) ? : '';
        $sortField = (string)(request()->query('sortField')) ? : 'id';

        return $this->model
            ->with($load)
            ->whereNull('reviewed_on')
            ->orderBy('application_date', 'desc')
            ->get();

      //  return $this->model->with($load)->get();

        if (strlen ($this->$whereField) > 0) {
            if(strlen ($this->$whereValue) < 1) {
                return $this->model
                    ->with($load)
                    ->whereNull($this->$whereField)
                    ->search($this->$searchFilter, null, true, true)
                    ->orderBy($this->$sortField, 'desc')
                    ->get();
            }
            return $this->model
                ->with($load)
                ->where($this->$whereField, $this->$whereValue)
                ->search($this->$searchFilter, null, true, true)
                ->orderBy($this->$sortField, 'desc')
                ->get();
        }else {
            return $this->model->search($this->$searchFilter, null, true, true)
                ->with($load)
                ->orderBy($this->$sortField, 'desc')
                ->get();
        }
    }/*

    public function getSum($field)
    {
        return $this->model->sum($field);
    }*/

}