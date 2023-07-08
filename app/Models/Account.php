<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 12/07/2019
 * Time: 05:44
 */

namespace App\Models;

use App\Traits\BranchFilterScope;
use App\Traits\BranchScope;
use Carbon\Carbon;
use Nicolaslopezj\Searchable\SearchableTrait;

class Account extends BaseModel
{
    use SearchableTrait, BranchScope;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'accounts';

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
        'account_number',
        'account_code',
        'account_name', // Will be member_id (For deposit accounts) // loan_id (For loan accounts)
        'account_type_id',
        'account_status_id',
        'other_details',
        'closed_on',

        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public $tenantColumns = ['branch_id'];


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
            'accounts.account_number' => 2,
            'accounts.account_code' => 2,
            'accounts.account_name' => 2,
            'accounts.other_details' => 1
        ]
    ];

    /**
     * Generate account numbers
     * Branch code, year, month, day and three random numbers
     */
    static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if(empty($model->account_number)) {
                $branchCode = Branch::find($model->branch_id)->branch_code;
                $random = substr(uniqid('', true),-3);
                $model->account_number = $branchCode.now()->year.now()->month.now()->day.$random;
            }
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function journalDebitEntries()
    {
        return $this->hasMany(Journal::class, 'debit_account_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function journalCreditEntries()
    {
        return $this->hasMany(Journal::class, 'credit_account_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accountType()
    {
        return $this->belongsTo(AccountType::class, 'account_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'account_name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class, 'account_name');
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
    public function accountStatus()
    {
        return $this->belongsTo(AccountStatus::class, 'account_status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function loans()
    {
        return $this->hasMany(Loan::class, 'account_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'account_id');
    }
}