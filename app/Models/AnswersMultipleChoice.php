<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswersMultipleChoice extends Model
{
    use HasFactory;

    protected $fillable = ['question_multiple_choice_id', 'answer', 'weight', 'diagnosis','strength_weakness_title',
        'strength_weakness',];

    public function questionMultipleChoice()
    {
        return $this->belongsTo(QuestionMultipleChoice::class);
    }
    public function clientAnswers() //Adicione se for usar
    {
        return $this->hasMany(ClientAnswer::class, 'multiple_choice_answer_id');
    }
}
