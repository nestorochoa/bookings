<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $usr_id
 * @property string $usr_name
 * @property string $usr_surname
 * @property integer $usr_type
 * @property string $usr_email
 * @property string $usr_username
 * @property string $usr_pass
 * @property string $usr_hash
 * @property string $usr_phone_main
 * @property string $usr_phone_sec
 * @property integer $bk_origin
 * @property string $bk_creation_date
 * @property integer $usr_deactive
 */
class BUsers extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'bk_users';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'usr_id';

    /**
     * @var array
     */
    protected $fillable = ['usr_name', 'usr_surname', 'usr_type', 'usr_email', 'usr_username', 'usr_pass', 'usr_hash', 'usr_phone_main', 'usr_phone_sec', 'bk_origin', 'bk_creation_date', 'usr_deactive'];
}
