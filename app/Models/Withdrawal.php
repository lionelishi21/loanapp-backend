<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 22/01/2020
 * Time: 18:08
 */

namespace App\Models;

use App\Traits\BranchFilterScope;
use App\Traits\BranchScope;
use Nicolaslopezj\Searchable\SearchableTrait;

class Withdrawal extends BaseModel
{
    use SearchableTrait, BranchScope;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'withdrawals';

    /**
     * Main table primary key
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id',
        'member_id',
        'amount',
        'withdrawal_date',
        'method_id',
        'withdrawal_number',

        'withdrawal_charges',
        'balance_before_withdrawal',
        'balance_after_withdrawal',
        'status',
        'mpesa_number',
        'first_name',
        'last_name',

        // Bank fields
        'cheque_number',
        'bank_name',
        'bank_branch',
        'cheque_date',

        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'withdrawals.amount' => 2,
            'withdrawals.withdrawal_date' => 2
        ]
    ];

    static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            $latest = $model->latest()->first();

            // Generate Withdrwal Number
            if ($latest) {
                $string = preg_replace("/[^0-9\.]/", '', $latest->withdrawal_number);
                $model->withdrawal_number =  'WTD-' . sprintf('%04d', $string+1);
            }else{
                $model->withdrawal_number = 'WTD-0001';
            }

        });
    }

    /**
     * @param $withdrawal_date
     */
    public function setWithdrawalDateAttribute($withdrawal_date)
    {
        $this->attributes['withdrawal_date'] = date('Y-m-d H:i:s', strtotime($withdrawal_date));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'method_id');
    }
}

