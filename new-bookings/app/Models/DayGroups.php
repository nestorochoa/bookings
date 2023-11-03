<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $bd_id
 * @property string $bd_date
 * @property integer $bd_instructor
 * @property integer $bd_inactive
 */
class DayGroups extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'bk_day_groups';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'bd_id';

    /**
     * @var array
     */
    protected $fillable = ['bd_date', 'bd_instructor', 'bd_inactive'];
}
