<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 12/07/2019
 * Time: 12:42
 */

namespace App\Models;

use Nicolaslopezj\Searchable\SearchableTrait;

class TransactionType extends BaseModel
{
    use SearchableTrait;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'transaction_types';

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
        'code',
        'description'
    ];

    // e.g interest_payments, principal_payment, penalty_payment, penalty_waiver
    // e.g Deposit, Withdrawal, Payment, Refund, Adjustment

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
            'transaction_types.code' => 2,
            'transaction_types.description' => 1,
        ]
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'transaction_type_id');
    }
}