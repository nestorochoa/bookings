<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $bk_id
 * @property integer $bk_group
 * @property string $hour_from
 * @property string $hour_to
 * @property string $bk_level
 * @property integer $bk_student
 * @property integer $bk_status
 * @property integer $bk_special_num
 * @property string $bk_createdate
 * @property string $bk_canceldate
 * @property string $bk_cancel_manager
 * @property integer $bk_cancel_count
 * @property integer $bk_lesson_level
 * @property string $bk_obs
 */
class Days extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'bk_days';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'bk_id';

    /**
     * @var array
     */
    protected $fillable = ['bk_group', 'hour_from', 'hour_to', 'bk_level', 'bk_student', 'bk_status', 'bk_special_num', 'bk_createdate', 'bk_canceldate', 'bk_cancel_manager', 'bk_cancel_count', 'bk_lesson_level', 'bk_obs'];
}
