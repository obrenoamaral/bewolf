<x-app-layout>
    <div class="p-10 bg-dark">
        <div class="flex flex-wrap justify-between my-4 container mx-auto max-w-5xl">
            <h2 class="text-2xl font-bold text-gray-100 mb-4 w-full md:w-auto">Perguntas Cadastradas</h2>
            <a href="{{ route('questions.create') }}"
               class="border border-gray-600 text-gray-100 p-4 rounded-lg hover:bg-[#262626] w-full md:w-auto text-center">
                Nova pergunta
            </a>
        </div>

        @if ($questions->isEmpty())
            <div class="text-gray-600 max-w-5xl mx-auto">Nenhuma pergunta cadastrada.</div>
        @else
            <div class="container max-w-5xl mx-auto overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                    <tr class="text-gray-100">
                        <th class="p-2 text-left text-gray-100">Pergunta</th>
                        <th class="p-2 text-left">Respostas</th>
                        <th class="p-2 text-center w-40">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($questions as $question)
                        <tr class="border-b border-b-gray-600 hover:bg-[#262626]">
                            <td class="border-b border-b-gray-600 p-2 font-medium rounded-bl-lg text-gray-100 break-words">{{ $question->question }}</td>
                            <td class="border-b border-b-gray-600 p-2 break-words">
                                <ul>
                                    @foreach ($question->answers as $answer)
                                        <li class="text-sm text-gray-100 flex justify-between">
                                            <span>
                                                <strong>{{ $answer->answer }}</strong> (Peso: {{ $answer->weight }})
                                                <br><span
                                                    class="text-xs text-gray-200">{{ $answer->diagnosis }} → {{ $answer->solution ?? 'Sem solução' }}</span>
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="p-2 text-center flex gap-2 w-40">
                                <button data-question-id="{{ $question->id }}"
                                        class="edit-button border border-blue-400 text-white px-6 py-1 rounded-lg cursor-pointer">
                                    <i class="pi pi-file-edit" style="color: #51A2FF"></i>
                                </button>
                                <form action="{{ route('questions.destroy', $question->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="border border-red-400 text-white px-6 py-1 rounded-lg cursor-pointer">
                                        <i class="pi pi-trash" style="color: #FF6467"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
        <div class="relative  min-h-screen flex items-center justify-center p-4">
            <div class="bg-[#1E1E1E] p-6 rounded-lg shadow-lg max-w-4xl w-full mx-4 text-gray-100 max-h-full overflow-y-auto">
                <h2 class="text-xl font-bold mb-4">Editar Pergunta</h2>
                <form id="editForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="questionId" name="questionId">
                    <label class="block mb-2 text-gray-300" for="questionText">Pergunta:</label>
                    <input type="text" id="questionText" name="question"
                           class="w-full p-2 bg-transparent rounded-md border border-gray-500 text-gray-100 focus:border-blue-500 outline-none" required>

                    <div class="mt-4">
                        <h3 class="text-lg font-semibold mb-2">Respostas</h3>
                        <div id="answersContainer">
                        </div>
                        <button type="button" id="addAnswerButton"
                                class="mt-2 bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white transition duration-200">Adicionar Resposta
                        </button>
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" id="cancelEditButton"
                                class="border border-gray-500 hover:border-gray-400 px-4 py-2 rounded-lg text-gray-300 transition duration-200">Cancelar
                        </button>
                        <button type="button" id="saveEditButton"
                                class="border border-blue-500 hover:border-blue-400 bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white transition duration-200">Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.edit-button');
            const editModal = document.getElementById('editModal');
            const questionText = document.getElementById('questionText');
            const answersContainer = document.getElementById('answersContainer');
            const addAnswerButton = document.getElementById('addAnswerButton');
            const cancelEditButton = document.getElementById('cancelEditButton');
            const editForm = document.getElementById('editForm');
            const questionId = document.getElementById('questionId');
            const saveEditButton = document.getElementById('saveEditButton');


            // Abrir modal e carregar dados
            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const questionIdValue = this.dataset.questionId;

                    fetch(`/questions/${questionIdValue}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`Erro na requisição: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(question => {
                            questionId.value = question.id;
                            questionText.value = question.question;
                            answersContainer.innerHTML = '';

                            question.answers.forEach(answer => {
                                addAnswer(answer);
                            });
                            editModal.classList.remove('hidden');
                        })
                        .catch(error => {
                            console.error("Erro ao buscar dados da pergunta:", error);
                            alert('Ocorreu um erro ao carregar os dados da pergunta.');
                        });
                });
            });

            // Cancelar edição
            cancelEditButton.addEventListener('click', () => {
                editModal.classList.add('hidden');
            });

            // Adicionar resposta
            addAnswerButton.addEventListener('click', () => {
                addAnswer();
            });

            // Função para adicionar/renderizar resposta
            function addAnswer(answer = null) {
                const answerDiv = document.createElement('div');
                answerDiv.classList.add("mb-4", "border", "border-gray-600", "p-4", "rounded-lg");
                answerDiv.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-300 mb-1">Resposta:</label>
                            <input type="text" name="answer" value="${answer ? answer.answer : ''}" class="w-full p-2 bg-transparent rounded-md text-gray-100" required>
                        </div>
                        <div>
                            <label class="block text-gray-300 mb-1">Peso:</label>
                            <input type="number" name="weight" value="${answer ? answer.weight : ''}" class="w-full p-2 bg-transparent rounded-md text-gray-100" required>
                        </div>
                        <div>
                            <label class="block text-gray-300 mb-1">Diagnóstico:</label>
                            <textarea name="diagnosis" class="w-full p-2 bg-transparent rounded-md text-gray-100">${answer ? answer.diagnosis : ''}</textarea>
                        </div>
                         <div>
                            <label class="block text-gray-300 mb-1">Solução:</label>
                            <textarea name="solution" class="w-full p-2 bg-transparent rounded-md text-gray-100">${answer ? (answer.solution ?? '') : ''}</textarea>
                        </div>
                        <div>
                            <label class="block text-gray-300 mb-1">Título para tabela do Relatório</label>
                            <input type="text"  name="strength_weakness_title" value="${answer ? answer.strength_weakness_title : ''}" class="w-full p-2 bg-transparent rounded-md text-gray-100">
                        </div>
                        <div>
                            <label class="block text-gray-300 mb-1">Forte/Fraco</label>
                            <select name="strength_weakness" class="w-full p-2 bg-dark rounded-md text-gray-100">
                                <option value="">Selecione</option>
                                <option value="strong" ${answer && answer.strength_weakness === 'strong' ? 'selected' : ''}>Forte</option>
                                <option value="weak" ${answer && answer.strength_weakness === 'weak' ? 'selected' : ''}>Fraco</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="mt-2 text-red-400 hover:text-red-500 remove-answer">Remover</button>
                `;
                answersContainer.appendChild(answerDiv);

                answerDiv.querySelector('.remove-answer').addEventListener('click', () => {
                    answerDiv.remove();
                });
            }

            // Salvar alterações
            saveEditButton.addEventListener('click', function () {
                const answerDivs = answersContainer.querySelectorAll('.mb-4');
                const answers = [];

                answerDivs.forEach(div => {
                    const answer = div.querySelector('input[name="answer"]').value;
                    const weight = div.querySelector('input[name="weight"]').value;
                    const diagnosis = div.querySelector('textarea[name="diagnosis"]').value;
                    const solution = div.querySelector('textarea[name="solution"]').value;
                    const strength_weakness_title = div.querySelector('input[name="strength_weakness_title"]').value;
                    const strength_weakness = div.querySelector('select[name="strength_weakness"]').value;

                    answers.push({
                        answer,
                        weight: parseInt(weight) || 0,
                        diagnosis,
                        solution,
                        strength_weakness_title,
                        strength_weakness
                    });
                });

                const payload = {
                    question: questionText.value,
                    answers: answers,
                    _method: 'PUT' // Para o Laravel entender que é um update
                };

                fetch(`/questions/${questionId.value}`, { //URL CORRETO
                    method: 'POST', // POST com _method: PUT
                    headers: {
                        'Content-Type': 'application/json', // ESSENCIAL
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload) // Envia como JSON
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw err;
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Sucesso:', data);
                        editModal.classList.add('hidden');
                        location.reload();
                    })
                    .catch((error) => {
                        console.error('Erro:', error);
                        let errorMessage = 'Ocorreu um erro ao salvar a pergunta.';
                        if (error.errors) {
                            errorMessage = Object.values(error.errors).flat().join('\n');
                        }
                        alert(errorMessage);
                    });
            });

        });
    </script>
</x-app-layout>
