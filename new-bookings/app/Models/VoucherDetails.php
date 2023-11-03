<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $vid
 * @property string $out_id
 * @property string $partial_code
 * @property string $postal_code_address
 * @property string $redeemed
 * @property string $redeem_date
 * @property integer $user_id
 * @property boolean $email_status
 * @property string $email_debug
 */
class VoucherDetails extends Model
{
    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'vid';

    /**
     * @var array
     */
    protected $fillable = ['out_id', 'partial_code', 'postal_code_address', 'redeemed', 'redeem_date', 'user_id', 'email_status', 'email_debug'];
}
