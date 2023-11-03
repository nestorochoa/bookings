<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $st_id
 * @property string $st_description
 * @property string $st_template
 */
class SmsTemplates extends Model
{
    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'st_id';

    /**
     * @var array
     */
    protected $fillable = ['st_description', 'st_template'];
}
