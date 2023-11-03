<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id_menu
 * @property string $description
 * @property string $link
 */
class Menu extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'Menu';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'id_menu';

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
    protected $fillable = ['description', 'link'];
}
