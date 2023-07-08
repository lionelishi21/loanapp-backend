<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 11/07/2019
 * Time: 12:45
 */

namespace App\Models;

use App\Traits\BranchFilterScope;
use App\Traits\BranchScope;
use Nicolaslopezj\Searchable\SearchableTrait;

class Asset extends BaseModel
{
    use SearchableTrait, BranchScope, BranchFilterScope;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'assets';

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
        'asset_number',
        'title',
        'description',
        'valuation_date',
        'valued_by',
        'valuer_phone',
        'valuation_amount',
        'location',
        'registration_number',
        'registered_to',
        'condition',
        'notes',

        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * @param $valuation_date
     */
    public function setValuationDateAttribute($valuation_date)
    {
        $this->attributes['valuation_date'] = date('Y-m-d H:i:s', strtotime($valuation_date));
    }

    /**
     * @param $title
     */
    public function setTitleAttribute($title)
    {
        $this->attributes['title'] = ucwords($title);
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
            'assets.member_id' => 2,
            'assets.asset_number' => 1,
            'assets.title' => 1,
            'assets.description' => 1,
            'assets.valuation_date' => 1,
            'assets.valued_by' => 1,
            'assets.valuer_phone' => 1,
            'assets.valuation_amount' => 1,
            'assets.location' => 1,
            'assets.registration_number' => 1,
            'assets.registered_to' => 1,
            'assets.condition' => 1,
            'assets.notes' => 1
        ]
    ];

    /**
     * Generate asset numbers
     */
    static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $random = substr(uniqid('', true),-2);
            $model->asset_number = now()->year.now()->month.now()->day.$random;
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany(AssetPhoto::class, 'asset_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function loanApplications()
    {
        return $this->belongsToMany(LoanApplication::class, 'asset_loan_applications', 'asset_id', 'loan_application_id');
    }
}