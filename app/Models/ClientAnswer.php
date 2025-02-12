<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'question_id', 'answer_id'];

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

    public function questionMultipleChoice() { // Nome do relacionamento (singular)
        return $this->belongsTo(QuestionMultipleChoice::class, 'question_multiple_choices_id'); // Nome da coluna corrigido
    }

    public function answerMultipleChoice() { // Nome do relacionamento (singular)
        return $this->belongsTo(AnswersMultipleChoice::class, 'multiple_choice_answer_id'); // Nome da coluna corrigido
    }
}
