<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $user_type
 * @property string $menu_id
 */
class UserAccess extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'user_access';

    /**
     * @var array
     */
    protected $fillable = [];
}
