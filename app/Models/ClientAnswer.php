<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAnswer extends Model
{
    protected $fillable = [
        'client_id',
        'question_id',
        'answer_id',
        'submission_id', // Adicione o submission_id aqui
        'question_type'
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

}
