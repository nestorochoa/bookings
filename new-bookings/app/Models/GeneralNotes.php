<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $gn_code
 * @property string $gn_html
 */
class GeneralNotes extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'bk_general_notes';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'gn_code';

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
    protected $fillable = ['gn_html'];
}
