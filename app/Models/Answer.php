<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = ['question_id', 'answer', 'diagnosis', 'solution', 'strength_weakness_title', 'strength_weakness', 'weight'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
