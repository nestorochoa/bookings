<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $st_id
 * @property integer $st_level
 * @property float $st_hours
 * @property string $st_observations
 * @property integer $st_n_students
 * @property integer $st_special
 * @property integer $st_payment_m
 * @property float $st_price
 * @property integer $st_saasu
 * @property float $st_penalty
 */
class StudentDetails extends Model
{
    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'st_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['st_level', 'st_hours', 'st_observations', 'st_n_students', 'st_special', 'st_payment_m', 'st_price', 'st_saasu', 'st_penalty'];
}
