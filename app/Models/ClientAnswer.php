<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAnswer extends Model
{
    // use HasFactory;  // Você não está usando HasFactory, então pode remover.

    protected $fillable = [
        'client_id',
        'question_id',
        'answer_id',
        'submission_id',
        'question_multiple_choices_id', // ADICIONADO!
        'multiple_choice_answer_id',  // ADICIONADO!
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function questionMultipleChoice()
    {
        return $this->belongsTo(QuestionMultipleChoice::class, 'question_multiple_choices_id');
    }

    public function multipleChoiceAnswer()
    {
        return $this->belongsTo(AnswersMultipleChoice::class, 'multiple_choice_answer_id');
    }
}
