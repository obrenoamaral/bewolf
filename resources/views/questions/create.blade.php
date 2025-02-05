<x-app-layout>
    <div class="max-w-5xl mx-auto rounded-xl pt-10">
        <a href="{{ route('questions.index') }}" class="border border-gray-400 text-gray-400 px-4 py-2 rounded-lg cursor-pointer">
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

        <form method="POST" action="{{ route('questions.store') }}">
            @csrf
            <!-- Campo da Pergunta -->
            <label class="block text-gray-100 font-medium">Pergunta:</label>
            <input name="question" type="text" required class="w-full p-2 rounded-lg mt-1 bg-transparent text-gray-100"/>

            <div id="answers-container">
                <div class="answer-group mt-4 p-4 border border-gray-600 rounded-lg">
                    <label class="block text-gray-100 font-medium">Resposta 1:</label>
                    <input name="answers[0][answer]" type="text" required placeholder="Resposta" class="w-full p-2 rounded-lg mt-1 bg-transparent text-gray-100"/>

                    <label class="block text-gray-100 font-medium mt-2">Peso:</label>
                    <input name="answers[0][weight]" type="number" required placeholder="Peso" class="w-full p-2 rounded-lg mt-1 bg-transparent text-gray-100"/>

                    <label class="block text-gray-100 font-medium mt-2">Diagnóstico:</label>
                    <input name="answers[0][diagnosis]" type="text" required placeholder="Diagnóstico" class="w-full p-2 rounded-lg mt-1 bg-transparent text-gray-100"/>

                    <label class="block text-gray-100 font-medium mt-2">Solução:</label>
                    <input name="answers[0][solution]" type="text" required placeholder="Solução" class="w-full p-2 bg-transparent rounded-lg mt-1 text-gray-100"/>
                </div>
            </div>

            <!-- Botão para Adicionar Nova Resposta -->
            <button type="button" id="add-answer" class="mt-3 text-blue-500 hover:text-blue-700 text-sm">
                + Adicionar Outra Resposta
            </button>

            <!-- Botão de Envio -->
            <button type="submit" class="w-full mt-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold p-2 rounded-lg">
                Cadastrar Pergunta
            </button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let answerIndex = 1;

            // Adicionar nova resposta
            document.getElementById("add-answer").addEventListener("click", function() {
                const container = document.getElementById("answers-container");
                const answerGroup = document.createElement("div");
                answerGroup.classList.add("answer-group", "mt-4", "p-4", "border", "border-gray-600", "rounded-lg");
                answerGroup.innerHTML = `
                    <label class="block text-gray-100 font-medium">Resposta ${answerIndex + 1}:</label>
                    <input name="answers[${answerIndex}][answer]" type="text" required placeholder="Resposta" class="w-full p-2 rounded-lg mt-1 bg-transparent text-gray-100"/>

                    <label class="block text-gray-100 font-medium mt-2">Peso:</label>
                    <input name="answers[${answerIndex}][weight]" type="number" required placeholder="Peso" class="w-full p-2 rounded-lg mt-1 bg-transparent text-gray-100"/>

                    <label class="block text-gray-100 font-medium mt-2">Diagnóstico:</label>
                    <input name="answers[${answerIndex}][diagnosis]" type="text" required placeholder="Diagnóstico" class="w-full p-2 rounded-lg mt-1 bg-transparent text-gray-100"/>

                    <label class="block text-gray-100 font-medium mt-2">Solução:</label>
                    <input name="answers[${answerIndex}][solution]" type="text" required placeholder="Solução" class="w-full p-2 rounded-lg mt-1 bg-transparent text-gray-100"/>

                    <button type="button" class="remove-answer mt-2 text-red-500 hover:text-red-700 text-sm">
                        Remover Resposta
                    </button>
                `;
                container.appendChild(answerGroup);
                answerIndex++;

                // Adicionar evento de remoção para o novo botão
                answerGroup.querySelector(".remove-answer").addEventListener("click", function() {
                    container.removeChild(answerGroup);
                });
            });
        });
    </script>
</x-app-layout>
