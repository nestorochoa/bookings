<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $tc_sku
 * @property float $tc_hours
 * @property integer $tc_special
 */
class TableCoupons extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'bk_table_coupons';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'tc_sku';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['tc_hours', 'tc_special'];
}
