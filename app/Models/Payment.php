<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 16/12/2018
 * Time: 11:12
 */

namespace App\Models;

use App\Traits\BranchFilterScope;
use App\Traits\BranchScope;
use Carbon\Carbon;
use Nicolaslopezj\Searchable\SearchableTrait;

class Payment extends BaseModel
{
    use SearchableTrait, BranchScope, BranchFilterScope;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payments';

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
        'method_id',
        'transaction_id',
        'payment_date',
        'receipt_number',
        'attachment',
        'notes',

        // Bank fields
        'cheque_number',
        'bank_name',
        'bank_branch',
        'cheque_date',

        // Mpesa fields
        'is_mpesa',
        'transaction_type',
        'trans_id',
        'trans_time',
       // 'trans_amount', //we already have amount field
        'business_short_code',
        'bill_ref_number',
        'invoice_number',  //loan_id or account number
        'mpesa_number',
        'mpesa_first_name',
        'mpesa_middle_name',
        'mpesa_last_name',
        'org_account_balance',
        'third_party_trans_id',

        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * @param $payment_date
     */
    public function setPaymentDateAttribute($payment_date)
    {
        $this->attributes['payment_date'] = date('Y-m-d H:i:s', strtotime($payment_date));
    }

    /**
     * @param $cheque_date
     */
    public function setChequeDateAttribute($cheque_date)
    {
        $this->attributes['cheque_date'] = date('Y-m-d H:i:s', strtotime($cheque_date));
    }

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
            'payments.amount' => 2,
            'payments.receipt_number' => 1,
            'payments.payment_date' => 4,
            'payment_methods.display_name' => 3,
            'members.first_name' => 1,
            'members.middle_name' => 1,
            'members.last_name' => 1,
            'members.id_number' => 1,
        ],
        'joins' => [
            'members' => ['payments.member_id','members.id'],
            'payment_methods' => ['payments.method_id','payment_methods.id'],
        ]
    ];

    static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            $latest = $model->latest()->first();

            if ($latest) {
                $string = preg_replace("/[^0-9\.]/", '', $latest->receipt_number);
                $model->receipt_number =  'RCT-' . sprintf('%04d', $string+1);
            }else{
                $model->receipt_number = 'RCT-0001';
            }

        });
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
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'method_id');
    }
}