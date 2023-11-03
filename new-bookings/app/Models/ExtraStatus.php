<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $es_id
 * @property string $es_description
 * @property boolean $es_report
 * @property boolean $es_active
 */
class ExtraStatus extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'extra_status';

    /**
     * @var array
     */
    protected $fillable = ['es_id', 'es_description', 'es_report', 'es_active'];
}
