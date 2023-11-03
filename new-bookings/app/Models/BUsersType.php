<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $usrt_id
 * @property string $usrt_desc
 */
class BUsersType extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'bk_users_type';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'usrt_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['usrt_desc'];
}
