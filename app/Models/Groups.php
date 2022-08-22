<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  App\Models\User;

class Groups extends Model
{
    use HasFactory;
    protected $table = "groups";
    function users()
    {
        return $this->hasMany(User::class, 'group_id', 'id');
    }
}
