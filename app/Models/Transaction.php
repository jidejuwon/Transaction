<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['user_id','amount','type','previous_balance', 'current_balance','reference'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
