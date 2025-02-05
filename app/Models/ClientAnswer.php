<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'question_id', 'answer_id'];

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
