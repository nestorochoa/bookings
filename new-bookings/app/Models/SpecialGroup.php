<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $sg_id
 * @property integer $sg_day
 * @property integer $sg_student
 * @property integer $sg_status
 * @property string $sg_date_cancel
 * @property string $sg_obs
 */
class SpecialGroup extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'special_group';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'sg_id';

    /**
     * @var array
     */
    protected $fillable = ['sg_day', 'sg_student', 'sg_status', 'sg_date_cancel', 'sg_obs'];
}
