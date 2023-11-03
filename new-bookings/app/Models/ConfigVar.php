<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $cfg_code
 * @property string $cfg_value
 */
class ConfigVar extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'config_var';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'cfg_code';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['cfg_value'];
}
