<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 03/06/2019
 * Time: 10:49
 */

namespace App\Models;

use Nicolaslopezj\Searchable\SearchableTrait;

class GeneralSetting extends BaseModel
{
    use SearchableTrait;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'general_settings';

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
        'business_name',
        'business_type',
        'email',
        'currency',
        'phone',
        //'country',
        //'county',
        //'town',
        'physical_address',
        'postal_address',
        'postal_code',
        'logo',
        'favicon',
        'date_format',
        'amount_thousand_separator',
        'amount_decimal_separator',
        'amount_decimal'
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
            'general_settings.business_name' => 2,
            'general_settings.business_type' => 1,
        ]
    ];
}