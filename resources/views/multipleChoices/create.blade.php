<x-app-layout>
    <div class="max-w-5xl mx-auto rounded-xl pt-10 px-4 sm:px-6">
        <a href="{{ route('multiple-choices.index') }}" class="border border-gray-400 text-gray-400 px-4 py-2 rounded-lg cursor-pointer">
            <i class="pi pi-arrow-left"></i>
        </a>
        <h2 class="text-xl font-bold text-gray-100 mb-4 mt-4">Cadastrar Pergunta</h2>

        @if(session('successMessage'))
            <div class="p-3 mb-4 bg-green-100 text-green-800 rounded-md">
                {{ session('successMessage') }}
            </div>
        @endif

        @if(session('errorMessage'))
            <div class="p-3 mb-4 bg-red-100 text-red-800 rounded-md">
                {{ session('errorMessage') }}
            </div>
        @endif

        <form method="POST" action="{{ route('multiple-choices.store') }}">
            @csrf

            <label class="block text-gray-100 font-medium">Pergunta:</label>
            <input name="question_title" type="text" required class="w-full p-2 rounded-lg mt-1 bg-transparent text-gray-100 break-words"/>

            <label class="block text-gray-100 font-medium mt-2">Título da Solução:</label>
            <input name="solution_title" type="text" class="w-full p-2 rounded-lg mt-1 bg-transparent text-gray-100 break-words"/>

            <div id="answers-container">
                <div class="answer-group mt-4 p-4 border border-gray-600 rounded-lg">
                    <label class="block text-gray-100 font-medium">Resposta 1:</label>
                    <input name="answers[0][answer]" type="text" required placeholder="Resposta" class="w-full p-2 rounded-lg mt-1 bg-transparent text-gray-100 break-words"/>

                    <label class="block text-gray-100 font-medium mt-2">Peso:</label>
                    <input name="answers[0][weight]" type="number" required placeholder="Peso" class="w-full p-2 rounded-lg mt-1 bg-transparent text-gray-100"/>

                    <label class="block text-gray-100 font-medium mt-2">Diagnóstico:</label>
                    <textarea name="answers[0][diagnosis]" required placeholder="Diagnóstico" class="w-full p-2 rounded-lg mt-1 bg-transparent text-gray-100 resize-y break-words"></textarea>

                    <label class="block text-gray-100 font-medium mt-2">Título Ponto forte/fraco:</label>
                    <input name="answers[0][strength_weakness_title]" type="text" placeholder="Título Ponto forte/fraco" class="w-full p-2 rounded-lg mt-1 bg-transparent text-gray-100 break-words"/>

                    <label class="block text-gray-100 font-medium mt-2">Ponto Forte ou Fraco:</label>
                    <select name="answers[0][strength_weakness]" class="w-full p-2 rounded-lg mt-1 bg-dark text-gray-100">
                        <option value="">Selecione</option>
                        <option value="strong">Forte</option>
                        <option value="weak">Fraco</option>
                    </select>
                </div>
            </div>

            <button type="button" id="add-answer" class="mt-3 text-blue-500 hover:text-blue-700 text-sm w-full sm:w-auto">
                + Adicionar Outra Resposta
            </button>

            <button type="submit" class="w-full mt-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold p-2 rounded-lg mb-6">
                Cadastrar Pergunta
            </button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let answerIndex = 1;

            const container = document.getElementById("answers-container");
            const addAnswerBtn = document.getElementById("add-answer");

            addAnswerBtn.addEventListener("click", function() {
                const answerGroup = document.createElement("div");
                answerGroup.classList.add("answer-group", "mt-4", "p-4", "border", "border-gray-600", "rounded-lg");
                answerGroup.innerHTML = `
                    <label class="block text-gray-100 font-medium">Resposta ${answerIndex + 1}:</label>
                    <input name="answers[${answerIndex}][answer]" type="text" required placeholder="Resposta" class="w-full p-2 rounded-lg mt-1 bg-transparent text-gray-100"/>

                    <label class="block text-gray-100 font-medium mt-2">Peso:</label>
                    <input name="answers[${answerIndex}][weight]" type="number" required placeholder="Peso" class="w-full p-2 rounded-lg mt-1 bg-transparent text-gray-100"/>

                    <label class="block text-gray-100 font-medium mt-2">Diagnóstico:</label>
                    <textarea name="answers[${answerIndex}][diagnosis]" required placeholder="Diagnóstico" class="w-full p-2 rounded-lg mt-1 bg-transparent text-gray-100 resize-y"></textarea>

                    <label class="block text-gray-100 font-medium mt-2">Título Ponto Forte ou Fraco:</label>
                    <input name="answers[${answerIndex}][strength_weakness_title]" type="text" placeholder="Título Ponto Forte ou Fraco" class="w-full p-2 rounded-lg mt-1 bg-transparent text-gray-100 break-words"/>

                    <label class="block text-gray-100 font-medium mt-2">Ponto Forte ou Fraco:</label>
                    <select name="answers[${answerIndex}][strength_weakness]" class="w-full p-2 rounded-lg mt-1 bg-dark text-gray-100">
                        <option value="">Selecione</option>
                        <option value="strong">Forte</option>
                        <option value="weak">Fraco</option>
                    </select>

                    <button type="button" class="remove-answer mt-2 text-red-500 hover:text-red-700 text-sm">
                        Remover Resposta
                    </button>`;

                container.appendChild(answerGroup);
                answerIndex++;
            });

            container.addEventListener("click", function(event) {
                if (event.target.classList.contains("remove-answer")) {
                    event.target.closest(".answer-group").remove();
                }
            });
        });
    </script>
</x-app-layout>
