<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $shv_hours
 * @property integer $shv_people
 * @property string $shv_description
 * @property float $shv_price
 * @property integer $shv_saasu_code
 */
class StudentHoursValue extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'student_hours_value';

    /**
     * @var array
     */
    protected $fillable = ['shv_description', 'shv_price', 'shv_saasu_code'];
}
