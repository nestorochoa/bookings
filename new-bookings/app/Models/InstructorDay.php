<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id_day
 * @property integer $id_instructor
 * @property integer $id_hour_ini
 * @property integer $id_hour_end
 */
class InstructorDay extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'bk_instructor_day';

    /**
     * @var array
     */
    protected $fillable = ['id_day', 'id_instructor', 'id_hour_ini', 'id_hour_end'];
}
