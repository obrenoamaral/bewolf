<x-app-layout>
    <div class="p-10 bg-dark">
        <div class="flex justify-between my-4 container mx-auto max-w-5xl">
            <h2 class="text-2xl font-bold text-gray-100 mb-4">Perguntas Cadastradas</h2>
            <a href="{{ route('questions.create') }}" class="border border-gray-600 text-gray-100 p-4  rounded-lg hover:bg-[#262626]">
                Nova pergunta
            </a>
        </div>

        @if ($questions->isEmpty())
            <div class="text-gray-600 max-w-5xl mx-auto">Nenhuma pergunta cadastrada.</div>
        @else
            <table class="container max-w-5xl mx-auto overflow-hidden">
                <thead>
                <tr class="text-gray-100">
                    <th class=" p-2 text-left text-gray-100">Pergunta</th>
                    <th class=" p-2 text-left">Respostas</th>
                    <th class=" p-2 text-center w-40">Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($questions as $question)
                    <tr class=" border-b border-b-gray-600 hover:bg-[#262626]">
                        <td class="border-b border-b-gray-600 p-2 font-medium rounded-bl-lg text-gray-100">{{ $question->question }}</td>
                        <td class="border-b border-b-gray-600 p-2">
                            <ul>
                                @foreach ($question->answers as $answer)
                                    <li class="text-sm text-gray-100 flex justify-between">
                                        <span>
                                            <strong>{{ $answer->answer }}</strong> (Peso: {{ $answer->weight }})
                                            <br><span class="text-xs text-gray-200">{{ $answer->diagnosis }} → {{ $answer->solution }}</span>
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="p-2 text-center flex gap-2 w-40">
                            <button data-question="{{ json_encode($question) }}" class="edit-button border border-blue-400 text-white px-6 py-1 rounded-lg cursor-pointer">
                                <i class="pi pi-file-edit" style="color: #51A2FF"></i>
                            </button>
                            <form action="{{ route('questions.destroy', $question->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="border border-red-400 text-white px-6 py-1 rounded-lg cursor-pointer">
                                    <i class="pi pi-trash" style="color: #FF6467"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div id="editModal" class="fixed inset-0 flex items-center justify-center bg-black hidden">
        <div class="bg-[#1E1E1E] p-6 rounded-lg shadow-lg max-w-4xl text-gray-100">
            <h2 class="text-xl font-bold mb-4">Editar Pergunta</h2>

            <label class="block mb-2 text-gray-300">Pergunta:</label>
            <input type="text" id="questionText" class="w-full p-2 bg-transparent rounded-md text-gray-100">

            <div class="mt-4">
                <h3 class="text-lg font-semibold mb-2">Respostas</h3>
                <div id="answersContainer">
                </div>
                <button type="button" id="addAnswerButton" class="mt-2 bg-green-600 px-4 py-2 rounded-lg text-white">Adicionar Resposta</button>
            </div>

            <div class="mt-4 flex justify-end gap-2">
                <button type="button" id="cancelEditButton" class="border border-gray-500 px-4 py-2 rounded-lg text-gray-300">Cancelar</button>
                <button type="button" id="saveEditButton" class="border border-blue-500 bg-blue-600 px-4 py-2 rounded-lg text-white">Salvar</button>
            </div>
        </div>
    </div>

    <script>
        const editButtons = document.querySelectorAll('.edit-button');
        const editModal = document.getElementById('editModal');
        const questionText = document.getElementById('questionText');
        const answersContainer = document.getElementById('answersContainer');
        const addAnswerButton = document.getElementById('addAnswerButton');
        const cancelEditButton = document.getElementById('cancelEditButton');
        const saveEditButton = document.getElementById('saveEditButton');

        editButtons.forEach(button => {
            button.addEventListener('click', () => {
                const question = JSON.parse(button.dataset.question);
                questionText.value = question.question;
                answersContainer.innerHTML = '';

                question.answers.forEach(answer => {
                    const answerDiv = document.createElement('div');
                    answerDiv.innerHTML = `
                        <input type="text" value="${answer.answer}" class="w-full p-2 bg-transparent rounded-md text-gray-100 mb-1">
                        <div class="flex gap-2 text-sm text-gray-300">
                            <input type="text" value="${answer.diagnosis}" class="flex-1 p-2 bg-transparent rounded-md" placeholder="Diagnóstico">
                            <input type="text" value="${answer.solution}" class="flex-1 p-2 bg-transparent rounded-md" placeholder="Solução">
                            <input type="number" value="${answer.weight}" class="w-16 p-2 bg-transparent rounded-md" placeholder="Peso">
                        </div>
                        <button type="button" class="mt-2 text-red-400 hover:text-red-500">Remover</button>
                    `;
                    answersContainer.appendChild(answerDiv);
                });

                editModal.classList.remove('hidden');
            });
        });

        cancelEditButton.addEventListener('click', () => {
            editModal.classList.add('hidden');
        });

        addAnswerButton.addEventListener('click', () => {
            const answerDiv = document.createElement('div');
            answerDiv.innerHTML = `
                <input type="text" class="w-full p-2 border border-gray-600 rounded-md text-gray-100 mb-1">
                <div class="flex gap-2 text-sm text-gray-300">
                    <input type="text" class="flex-1 p-2 border border-gray-600 rounded-md" placeholder="Diagnóstico">
                    <input type="text" class="flex-1 p-2 border border-gray-600 rounded-md" placeholder="Solução">
                    <input type="number" class="w-16 p-2 border border-gray-600 rounded-md" placeholder="Peso">
                </div>
                <button type="button" class="mt-2 text-red-400 hover:text-red-500">Remover</button>
            `;
            answersContainer.appendChild(answerDiv);
        });

        saveEditButton.addEventListener('click', () => {
            // Aqui você precisará adicionar a lógica para salvar as alterações no banco de dados
            // Você pode usar fetch ou axios para enviar os dados para o seu backend
            // Lembre-se de incluir o token CSRF nas requisições
            editModal.classList.add('hidden');
        });

    </script>
</x-app-layout>
