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
    public function question() // Nome corrigido para seguir a convenção
    {
        return $this->belongsTo(QuestionMultipleChoice::class);
    }
}
