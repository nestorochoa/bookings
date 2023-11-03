<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $uid
 * @property string $code_group
 * @property string $description
 * @property integer $number_student
 * @property integer $special_group
 * @property integer $number_hours
 * @property string $date_creation
 * @property string $expiry_date
 */
class VoucherHead extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'voucher_head';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * @var array
     */
    protected $fillable = ['code_group', 'description', 'number_student', 'special_group', 'number_hours', 'date_creation', 'expiry_date'];
}
