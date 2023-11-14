<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
class BUsers extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'usr_name',
        'usr_surname',
        'usr_type',
        'usr_email',
        'usr_username',
        'usr_pass',
        'usr_hash',
        'usr_phone_main',
        'usr_phone_sec',
        'bk_origin',
        'bk_creation_date',
        'usr_deactive'
    ];

    public function getAuthPassword()
    {
        return $this->usr_hash;
    }
    public function getAuthIdentifier()
    {
        return $this->usr_email;
    }
    public function getAuthIdentifierName()
    {
        return "usr_email";
    }
    public function getEmailAttribute()
    {
        return $this->usr_email;
    }

    public function getEmailForPasswordReset()
    {
        return $this->usr_email;
    }
    public function getReminderEmail()
    {
        return $this->usr_email;
    }
}