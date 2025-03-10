<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'company', 'email', 'phone', 'como_chegou'];

    public function answers()
    {
        return $this->hasMany(ClientAnswer::class);
    }
}
