<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 27/08/2019
 * Time: 15:42
 */

namespace App\Models;

use App\Traits\BranchFilterScope;
use App\Traits\BranchScope;
use Nicolaslopezj\Searchable\SearchableTrait;

class Expense extends BaseModel
{
    use SearchableTrait, BranchScope, BranchFilterScope;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'expenses';

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
        'category_id',
        'title',
        'amount',
        'expense_date',
        'attachment',
        'notes',

        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * @param $expense_date
     */
    public function setExpenseDateAttribute($expense_date)
    {
        $this->attributes['expense_date'] = date('Y-m-d H:i:s', strtotime($expense_date));
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
            'expenses.title' => 1,
            'expenses.amount' => 1,
            'expenses.expense_date' => 2,
            'expense_categories.name' => 1,
        ],
        'joins' => [
            'expense_categories' => ['expenses.category_id','expense_categories.id'],
        ]
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Account::class, 'category_id');
    }
}