<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['amount','type','previous_balance', 'current_balance'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
