<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionMultipleChoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_title'
    ];

    public function answers() // Nome corrigido para seguir a convenção
    {
        return $this->hasMany(AnswersMultipleChoice::class);
    }
}
