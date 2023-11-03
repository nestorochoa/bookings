<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $dl_date
 * @property string $dl_hour_ini
 * @property string $dl_hour_end
 */
class DayLimits extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'bk_day_limits';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'dl_date';

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
    protected $fillable = ['dl_hour_ini', 'dl_hour_end'];
}
