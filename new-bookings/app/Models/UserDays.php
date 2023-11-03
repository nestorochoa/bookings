<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $ud_id
 * @property integer $ud_id_days
 * @property integer $ud_user
 * @property string $ud_hour_ini
 * @property string $ud_hour_end
 * @property integer $ud_status
 */
class UserDays extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'bk_user_days';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'ud_id';

    /**
     * @var array
     */
    protected $fillable = ['ud_id_days', 'ud_user', 'ud_hour_ini', 'ud_hour_end', 'ud_status'];
}
