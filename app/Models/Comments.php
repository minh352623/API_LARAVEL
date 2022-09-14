<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class Comments extends Model
{
    use HasFactory;
    function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
