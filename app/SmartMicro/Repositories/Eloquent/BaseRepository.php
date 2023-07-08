<?php

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\GeneralSetting;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;

/**
 * Class BaseRepository
 * @package App\Sproose\Repositories\Eloquent
 */
abstract class BaseRepository {

    protected $orderBy  = array('updated_at', 'desc');

    protected $model, $transformer;

    /**
     * Set number of records to return
     * @return int
     */
    private function limit(){
        return (int)(request()->query('limit')) ? : 5;
    }

    /**
     * @return string
     */
    private function sortField () {
        return (string)(request()->query('sortField')) ? : 'id';
    }

    /**
     * @return string
     */
    private function sortDirection() {
        return (string)(request()->query('sortDirection')) ? : 'desc';
    }

    /**
     * @return string
     */
    private function searchFilter() {
        return (string)(request()->query('filter')) ? : '';
    }

    /**
     * @return string
     */
    private function whereField() {
        return (string)(request()->query('whereField')) ? : '';
    }

    /**
     * @return string
     */
    private function whereValue() {
        return (string)(request()->query('whereValue')) ? : '';
    }

    /**
     * @param array $load
     * @return mixed
     */
    public function getAllNoSearchPaginate($load = array()) {
        return $this->model
            ->with($load)
            ->orderBy($this->sortField(), $this->sortDirection())
            ->paginate($this->limit());
    }

    /**
     * @param array $load
     * @return mixed
     */
    public function getAll($load = array())
    {
        return $this->model->with($load)->get();
    }

    /**
     * @param array $load
     * @return mixed
     */
    public function getAllPaginate($load = array()){

        if (strlen ($this->whereField()) > 0) {
            if(strlen ($this->whereValue()) < 1) {
                return $this->model
                    ->with($load)
                    ->whereNull($this->whereField())
                    ->search($this->searchFilter(), null, true, true)
                    ->orderBy($this->sortField(), $this->sortDirection())
                    ->paginate($this->limit());
            }
            return $this->model
                ->with($load)
                ->where($this->whereField(), $this->whereValue())
                ->search($this->searchFilter(), null, true, true)
                ->orderBy($this->sortField(), $this->sortDirection())
                ->paginate($this->limit());
        }else {
            return $this->model->search($this->searchFilter(), null, true, true)
                ->with($load)
                ->orderBy($this->sortField(), $this->sortDirection())
                ->paginate($this->limit());
        }
    }

    /**
     * Fetch data used for select drop down ui
     * @param $select
     * @param array $load
     * @return array
     */
    public function listAll($select, $load = array()) {

        array_push($select, 'id');

        $data = [];
        try{
            if($load){
                $data =  $this->model->with($load)->get($select);
            }else
                $data =  $this->model->get($select);
        }catch(\Exception $e){}

        return $data;
    }

    /**
     * Fetch a single item from db table.
     * Also load with relationships in load
     * @param $id
     * @param array $load
     * @return mixed
     */
    public function getById($id, $load = array())
    {
        if(!empty($load))
        {
            return $this->model->with($load)->find($id);
        }
        return $this->model->find($id);
    }

    /**
     * Get the first record
     * @return mixed
     */
    public function getFirst()
    {
        return $this->model->first();
    }

    /**
     * @param $count
     * @param array $load
     * @return mixed
     */
    public function getLatest($count, $load = array())
    {
        return $this->model->with($load)->latest()->limit($count)->get();
    }

    /**
     * @param $count
     * @param $field
     * @param $value
     * @param array $load
     * @return mixed
     */
    public function getLatestWhere($count, $field, $value, $load = array())
    {
        return $this->model->with($load)->where($field, $value)->latest()->limit($count)->first();
    }

    /**
     * @param $field
     * @return mixed
     */
    public function getSum($field)
    {
        return $this->model->sum($field);
    }

    /**
     * Fetch multiple specified orders
     * @param array $ids comma separated list of ids to fetch for
     * @param array $load related data
     * @return mixed
     */
    public function getByIds($ids = array(), $load = array())
    {
        $query =  $this->model->with($load)->whereIn('id', $ids);
        $data = $query->paginate($this->limit());
        return $data;
    }

    /**
     * @param $field
     * @param $value
     * @param array $load
     * @return mixed
     */
    public function getWhere($field, $value, $load = array())
    {
        $data =  $this->model->with($load)->where($field, $value)->orderBy('updated_at', 'desc')->first();
        return $data;
    }

    /**
     * @param $field
     * @param array $values
     * @param array $load
     * @return mixed
     */
    public function getManyWhere($field, $values = array(), $load = array())
    {
        //sort
        $sortDirection = \Request::input('sort_direction') ?: 'ASC';

        if( null!=$this->transformer )
            $sortProperty = $this->transformer->reverse(\Request::input('sort_property'));

        if(isset($sortProperty) && $sortProperty != false)
        {
            $data = $this->model->with($load)->whereIn($field, $values)->orderBy($sortProperty, $sortDirection)->paginate($this->limit());
        }else
            $data =  $this->model->with($load)->whereIn($field, $values)->paginate($this->limit());

        return $data;
    }

    /**
     * @param $filters
     * @param array $pagination
     * @param array $load
     * @return mixed
     */
    public function getFiltered($filters, $pagination = array(), $load = array())
    {
        if(isset($pagination) && array_key_exists('limit', $pagination)){
            $limit = $pagination['limit'];
        }else{
            $limit = \Request::input('limit') ?: 10;
        }

        if(isset($pagination) && array_key_exists('page', $pagination)){
            $page = $pagination['page'];
        }else
            $page = 1;

        $data = $this->model->with($load);

        foreach ($filters as $filter) {
            $data = $this->applyFilter($filter, $data);
        }

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        $data = $data->paginate($limit);

        return $data;
    }

    /**
     * @param $filter
     * @param $data
     * @return mixed
     */
    private function applyFilter($filter, $data)
    {
        $whereOperators = [
            'eq'   => '=',
            'neq'  => '!=',
            'gt'   => '>',
            'gte'  => '>=',
            'lt'   => '<',
            'lte'  => '<=',
            'like' => 'LIKE',
        ];

        if (array_key_exists($filter['operator'], $whereOperators)) {
            $data = $data->where($filter['field'], $whereOperators[$filter['operator']], $filter['value']);
        }

        if ($filter['operator'] == 'in') {
            $data = $data->whereIn($filter['field'], $filter['value']);
        }

        if ($filter['operator'] == 'notin') {
            $data = $data->whereNotIn($filter['field'], $filter['value']);
        }

        if ($filter['operator'] == 'between') {
            $data = $data->whereBetween($filter['field'], $filter['value']);
        }

        if ($filter['operator'] == 'notbetween') {
            $data = $data->whereNotBetween($filter['field'], $filter['value']);
        }
        return $data;
    }

    /**
     * @param array $data
     * @return null
     */
    public function create(array $data)
    {
        try{
            return $this->model->create($data);
        }catch (\Exception $exception){
            report($exception);
        }
        return null;
    }

    /**
     * @param array $data
     * @param $id
     * @return array
     */
    public function update(array $data, $id)
    {
        try{
            $record = $this->model->find($id);

            if(is_null($record))
                throw new ModelNotFoundException('Record not found');

            if(isset($record)){
                return $record->update($data);
            }

        }catch (\Exception $exception){
            report($exception);
        }
        return null;
    }

    /**
     * Remove a record from db
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        try{
            return $this->model->destroy($id);
        }catch (\Exception $exception){
            report($exception);
        }
        return false;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function firstOrCreate(array $data)
    {
        return $this->model->firstOrCreate($data);
    }

    /**
     * @param array $load
     * @return mixed
     */

    public function first($load = array())
    {
        if(!empty($load))
        {
            return $this->model->with($load)->first();
        }
        return $this->model->first();
    }

    /**
     * Count the number of specified model records in the database
     *
     * @return int
     */
    public function count()
    {
        return $this->model->count();
    }

    /**
     * @param $loanId
     * @param array $load
     * @return mixed
     */
    public function getPendingDueRepayment($loanId, $load = array())
    {
        $data =  $this->model->with($load)
            ->where('loan_id', $loanId)
            ->where('paid_on', null)
            ->orderBy('updated_at', 'desc')
            ->first();
        return $data;
    }

    /**
     * @param $amount
     * @return string
     */
    public function formatMoney($amount) {
        return number_format($amount, $this->amountDecimal(), $this->amountDecimalSeparator(), $this->amountThousandSeparator());
    }

    /**
     * @param $date
     * @return false|string
     */
    public function formatDate($date){
        return $new_date_format = date($this->dateFormat(), strtotime($date));
    }

    /**
     * @return string
     */
    private function dateFormat(){
       $format = GeneralSetting::select('date_format')->first()->date_format;

       if(isset($format))
           return $format;
       return 'd-m-Y';
    }

    /**
     * @return string
     */
    private function amountThousandSeparator() {
        $separator = GeneralSetting::select('amount_thousand_separator')->first()->amount_thousand_separator;

        if(isset($separator))
            return $separator;
        return ',';
    }

    /**
     * @return string
     */
    private function amountDecimalSeparator() {
        $separator = GeneralSetting::select('amount_decimal_separator')->first()->amount_decimal_separator;

        if(isset($separator))
            return $separator;
        return '.';
    }

    /**
     * @return int
     */
    private function amountDecimal() {
        $separator = GeneralSetting::select('amount_decimal')->first()->amount_decimal;

        if(isset($separator))
            return (int)$separator;
        return 2;
    }
}