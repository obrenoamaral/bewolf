<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionMultipleChoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_title',
        'solution_title',
    ];

    public function answersMultipleChoices() {
        return $this->hasMany(AnswersMultipleChoice::class);
    }
    public function clientAnswers()
    {
        return $this->hasMany(ClientAnswer::class);
    }
}
