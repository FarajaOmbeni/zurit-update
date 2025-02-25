<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventsFeedback extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'venue', 'comprehensiveness', 'relevance', 'recommendation', 'return_client', 'value_for_money', 'valuable_aspect', 'improvement', 'suggestion', 'fav_trainor'];
}
