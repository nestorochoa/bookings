<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $sl_id
 * @property string $sl_description
 */
class StudentLevel extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'student_level';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'sl_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['sl_description'];
}
