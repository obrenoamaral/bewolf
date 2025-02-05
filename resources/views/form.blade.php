<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Perguntas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-6 text-center uppercase">Mapeamento de Diagnóstico Empresarial</h1>
    <form id="questionForm" action="{{ route('form.submit') }}" method="POST" class="flex flex-col">
        @csrf
        @foreach ($questions as $question)
            <div class="mb-6">
                <p class="font-semibold mb-2">{{ $question->question }}</p>
                @foreach ($question->answers as $answer)
                    <label class="block mb-2">
                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}" required>
                        {{ $answer->answer }}
                    </label>
                @endforeach
            </div>
        @endforeach
        <button type="submit" class="bg-black text-white px-4 py-2 hover:bg-gray-800 w-40 mx-auto">Enviar Respostas</button>
    </form>
</div>

<!-- Popup para coletar informações -->
<div id="popup" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center flex maw-w-3xl hidden">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-xl font-bold mb-4 text-center">Receba o diagnótico no seu e-mail! Preencha as informações abaixo</h2>
        <form id="infoForm" action="{{ route('form.submitInfo') }}" method="POST">
            @csrf
            <input type="hidden" name="answers" id="answersInput">
            <div class="mb-4">
                <label for="name" class="block mb-2">Nome:</label>
                <input type="text" name="name" id="name" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="company" class="block mb-2">Nome da Empresa:</label>
                <input type="text" name="company" id="company" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block mb-2">Email:</label>
                <input type="email" name="email" id="email" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="phone" class="block mb-2">Telefone:</label>
                <input type="text" name="phone" id="phone" class="w-full p-2 border rounded" required>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Enviar</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('questionForm').addEventListener('submit', function (e) {
        e.preventDefault();

        // Coleta as respostas do formulário
        const formData = new FormData(this);
        const answers = {};

        formData.forEach((value, key) => {
            if (key.startsWith('answers[')) {
                const questionId = key.match(/\d+/)[0]; // Extrai apenas o número do ID da questão
                answers[questionId] = value;
            }
        });

        // Converte para JSON e armazena no input hidden
        document.getElementById('answersInput').value = JSON.stringify(answers);

        // Exibe o popup para coletar informações adicionais
        document.getElementById('popup').classList.remove('hidden');
    });

</script>
</body>
</html>
