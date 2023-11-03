<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $company_id
 * @property string $company_name
 * @property string $company_logo
 * @property string $company_abn
 */
class Company extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'Company';

    /**
     * @var array
     */
    protected $fillable = ['company_id', 'company_name', 'company_logo', 'company_abn'];
}
