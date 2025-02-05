<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnostic extends Model
{
    use HasFactory;

    protected $fillable = ['question_id', 'answer', 'diagnosis', 'solution'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
