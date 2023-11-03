<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $sh_id
 * @property integer $sh_ext_id
 * @property integer $sh_from
 * @property integer $sh_to
 * @property string $sh_mobile
 * @property string $sh_sms
 * @property string $sh_status
 * @property string $sh_date
 */
class SmsHistory extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'sms_history';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'sh_id';

    /**
     * @var array
     */
    protected $fillable = ['sh_ext_id', 'sh_from', 'sh_to', 'sh_mobile', 'sh_sms', 'sh_status', 'sh_date'];
}
