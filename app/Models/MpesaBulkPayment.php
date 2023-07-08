<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 24/01/2020
 * Time: 09:14
 */

namespace App\Models;

use App\Traits\BranchFilterScope;
use App\Traits\BranchScope;
use Nicolaslopezj\Searchable\SearchableTrait;

class MpesaBulkPayment extends BaseModel
{
    use SearchableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mpesa_bulk_payments';

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
        'transaction_amount',
        'transaction_receipt',
        'b2C_recipientIs_registered_customer',
        'b2C_charges_paid_account_available_funds',
        'receiver_party_public_name',
        'transaction_completed_date_time',
        'b2C_utility_account_available_funds',
        'b2C_working_account_available_funds'
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
            'mpesa_bulk_payments.transaction_amount' => 2,
            'mpesa_bulk_payments.transaction_receipt' => 1
        ]
    ];
}

