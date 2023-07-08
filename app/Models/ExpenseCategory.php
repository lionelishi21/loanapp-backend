<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 27/08/2019
 * Time: 15:46
 */

namespace App\Models;

use Nicolaslopezj\Searchable\SearchableTrait;

class ExpenseCategory extends BaseModel
{
    use SearchableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'expense_categories';

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
        'account_type_id',
        'account_name',
        'description'
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
            'expense_categories.account_name' => 2,
            'expense_categories.description' => 1,
        ]
    ];

    /**
     * Fill account type
     */
    static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $expenseTypeId = AccountType::where('code', EXPENSE_CODE)->select('id')->first()['id'];
            $model->account_type_id = $expenseTypeId;
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category_id');
    }
}