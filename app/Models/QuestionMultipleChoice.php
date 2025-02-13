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

    public function answersMultipleChoice()
    {
        return $this->hasMany(AnswersMultipleChoice::class, 'question_multiple_choice_id', 'id');
    }
    public function clientAnswers()
    {
        return $this->hasMany(ClientAnswer::class);
    }
}
