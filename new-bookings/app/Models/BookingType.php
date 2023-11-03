<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $bt_id
 * @property string $bt_description
 */
class BookingType extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'booking_type';

    /**
     * @var array
     */
    protected $fillable = ['bt_id', 'bt_description'];
}
