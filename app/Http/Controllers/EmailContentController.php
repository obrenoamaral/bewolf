<?php

namespace App\Http\Controllers;

use App\Models\EmailContent;
use Illuminate\Http\Request;

class EmailContentController extends Controller
{
    public function index()
    {
        $emailContent = EmailContent::first();
        return view('emails.report', compact('emailContent'));
    }

    public function edit()
    {
        $content = EmailContent::first() ?? new EmailContent();
        return view('emails.edit', compact('content'));
    }


    public function update(Request $request)
    {
        $request->validate([
            'greeting' => 'nullable|string|max:255',
            'intro_text' => 'nullable|string',
            'closing_text' => 'nullable|string',
            'button_text' => 'nullable|string|max:255', // Validação para o texto do botão
            'button_link' => 'nullable|string|max:255|url', // Validação para o link (deve ser uma URL válida)
        ]);

        // Verifica se já existe um registro, se não, cria um novo
        $content = EmailContent::first();

        if (!$content) {
            $content = new EmailContent();
        }

        $content->fill($request->all());
        $content->save(); // Salva no banco

        return redirect()->route('email.edit')->with('success', 'Conteúdo atualizado com sucesso!');
    }
}
