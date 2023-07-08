<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/11/2019
 * Time: 20:23
 */

namespace App\Models;

use App\Traits\BranchFilterScope;
use App\Traits\BranchScope;
use Nicolaslopezj\Searchable\SearchableTrait;

class Capital extends BaseModel
{
    use SearchableTrait, BranchScope;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'capitals';

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
        'fiscal_year_id',
        'method_id',
        'capital_date',
        'amount',
        'description',
        'created_by'
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
            'capitals.amount' => 2,
            'capitals.description' => 1,
            'branches.name' => 1,
        ],
        'joins' => [
            'branches' => ['capitals.branch_id','branches.id'],
        ]
    ];

    /**
     * @param $capital_date
     */
    public function setCapitalDateAttribute($capital_date)
    {
        $this->attributes['capital_date'] = date('Y-m-d H:i:s', strtotime($capital_date));
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