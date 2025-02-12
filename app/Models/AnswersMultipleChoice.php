<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswersMultipleChoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_multiple_choice_id',
        'answer',
        'weight',
        'diagnosis',
    ];

    // Relacionamento com a pergunta
    public function questionMultipleChoice() { // Nome do relacionamento (singular)
        return $this->belongsTo(QuestionMultipleChoice::class);
    }

    public function clientAnswers()
    {
        return $this->hasMany(ClientAnswer::class);
    }
}
