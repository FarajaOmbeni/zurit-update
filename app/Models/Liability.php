<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Liability extends Model
{
    use HasFactory;
    protected $table = 'liabilities';
    protected $fillable = ['user_id','liability_description','liability_value'];
}
