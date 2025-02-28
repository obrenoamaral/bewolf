<x-app-layout>
    <div class="p-10 bg-dark">
        <div class="flex flex-wrap justify-between my-4 container mx-auto max-w-5xl">
            <h2 class="text-2xl font-bold text-gray-100 mb-4 w-full md:w-auto">Perguntas Cadastradas</h2>
            <a href="{{ route('multiple-choices.create') }}" class="border border-gray-600 text-gray-100 p-4 rounded-lg hover:bg-[#262626] w-full md:w-auto text-center">
                Nova pergunta
            </a>
        </div>

        @if ($questionsMultipleChoice->isEmpty())
            <div class="text-gray-600 max-w-5xl mx-auto">Nenhuma pergunta cadastrada.</div>
        @else
            <div class="container max-w-5xl mx-auto overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                    <tr class="text-gray-100">
                        <th class="p-2 text-left text-gray-100">Pergunta</th>
                        <th class="p-2 text-left text-gray-100">Título da Solução</th>
                        <th class="p-2 text-left">Respostas</th>
                        <th class="p-2 text-center w-40">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($questionsMultipleChoice as $question)
                        <tr class="border-b border-b-gray-600 hover:bg-[#262626]">
                            <td class="border-b border-b-gray-600 p-2 font-medium rounded-bl-lg text-gray-100 break-words">{{ $question->question_title }}</td>
                            <td class="border-b border-b-gray-600 p-2 text-gray-100 break-words">{{ $question->solution_title }}</td>
                            <td class="border-b border-b-gray-600 p-2 break-words">
                                <ul>
                                    @foreach ($question->answersMultipleChoice as $answer)
                                        <li class="text-sm text-gray-100 flex justify-between">
                                            <span>
                                                <strong>{{ $answer->answer }}</strong> (Peso: {{ $answer->weight }})
                                                <br><span class="text-xs text-gray-200">{{ $answer->diagnosis }}</span>
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="p-2 text-center flex gap-2 w-40">
                                <button
                                    data-question="{{ json_encode($question) }}"
                                    data-answers="{{ json_encode($question->answersMultipleChoice) }}"
                                    class="edit-button border border-blue-400 text-white px-6 py-1 rounded-lg cursor-pointer"
                                >
                                    <i class="pi pi-file-edit" style="color: #51A2FF"></i>
                                </button>
                                <form action="{{ route('multiple-choices.destroy', $question->id) }}" method="POST" class="inline">
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
            </div>
        @endif
    </div>

    <div id="editModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden overflow-y-auto">
        <div class="bg-[#1E1E1E] p-6 rounded-lg shadow-lg max-w-4xl w-full mx-4 text-gray-100">
            <h2 class="text-xl font-bold mb-4">Editar Pergunta</h2>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="questionId">
                <label class="block mb-2 text-gray-300">Pergunta:</label>
                <input type="text" id="questionText" name="question_title" class="w-full p-2 bg-transparent rounded-md text-gray-100 break-words" required>

                <label class="block mb-2 text-gray-300">Título da Solução:</label>
                <input type="text" id="solutionTitleText" name="solution_title" class="w-full p-2 bg-transparent rounded-md text-gray-100 break-words">

                <div class="mt-4">
                    <h3 class="text-lg font-semibold mb-2">Respostas</h3>
                    <div id="answersContainer">
                    </div>
                    <button type="button" id="addAnswerButton" class="mt-2 bg-green-600 px-4 py-2 rounded-lg text-white">Adicionar Resposta</button>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" id="cancelEditButton" class="border border-gray-500 px-4 py-2 rounded-lg text-gray-300">Cancelar</button>
                    <button type="submit" id="saveEditButton" class="border border-blue-500 bg-blue-600 px-4 py-2 rounded-lg text-white">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const editButtons = document.querySelectorAll(".edit-button");
            const editModal = document.getElementById("editModal");
            const questionText = document.getElementById("questionText");
            const solutionTitleText = document.getElementById("solutionTitleText"); // Novo input
            const answersContainer = document.getElementById("answersContainer");
            const addAnswerButton = document.getElementById("addAnswerButton");
            const cancelEditButton = document.getElementById("cancelEditButton");
            const editForm = document.getElementById("editForm");
            const questionId = document.getElementById("questionId");

            editButtons.forEach((button) => {
                button.addEventListener("click", () => {
                    const question = JSON.parse(button.dataset.question);
                    const answers = JSON.parse(button.dataset.answers);

                    questionId.value = question.id;
                    questionText.value = question.question_title;
                    solutionTitleText.value = question.solution_title; // Preenche o input
                    answersContainer.innerHTML = "";

                    if (answers) {
                        answers.forEach((answer) => {
                            addAnswer(answer);
                        });
                    }

                    editModal.classList.remove("hidden");
                });
            });

            cancelEditButton.addEventListener("click", () => {
                editModal.classList.add("hidden");
            });

            addAnswerButton.addEventListener("click", () => {
                addAnswer();
            });

            function addAnswer(answer = null) {
                const answerDiv = document.createElement("div");
                answerDiv.classList.add("mb-2");

                answerDiv.innerHTML = `
            <input type="text" value="${answer ? answer.answer : ""}" class="w-full p-2 bg-transparent rounded-md text-gray-100 mb-1 answer-input" required>
            <div class="flex gap-2 text-sm text-gray-300">
                <textarea class="flex-1 p-2 bg-transparent rounded-md diagnosis-input" placeholder="Diagnóstico">${answer ? answer.diagnosis : ""}</textarea>
                <input type="number" value="${answer ? answer.weight : ""}" class="w-16 p-2 bg-transparent rounded-md weight-input" placeholder="Peso" required>
            </div>
            <button type="button" class="mt-2 text-red-400 hover:text-red-500 remove-answer">Remover</button>
        `;

                answersContainer.appendChild(answerDiv);

                const removeButton = answerDiv.querySelector(".remove-answer");
                removeButton.addEventListener("click", () => {
                    answersContainer.removeChild(answerDiv);
                });
            }

            editForm.addEventListener("submit", (event) => {
                event.preventDefault();

                const payload = {
                    question_title: questionText.value,
                    solution_title: solutionTitleText.value, // Inclui o novo campo
                    answers: [],
                };

                const answerDivs = document.querySelectorAll("#answersContainer > div");
                answerDivs.forEach((answerDiv) => {
                    const answerInput = answerDiv.querySelector(".answer-input");
                    const diagnosisInput = answerDiv.querySelector(".diagnosis-input");
                    const weightInput = answerDiv.querySelector(".weight-input");

                    payload.answers.push({
                        answer: answerInput.value,
                        diagnosis: diagnosisInput.value,
                        weight: parseInt(weightInput.value, 10) || 0,
                    });
                });

                fetch(`/multiple-choices/${questionId.value}`, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(payload),
                })
                    .then((response) => {
                        if (response.ok) {
                            location.reload();
                        } else {
                            return response.json().then((data) => {
                                alert("Erro ao salvar: " + (data.message || "Erro desconhecido"));
                            });
                        }
                    })
                    .catch((error) => {
                        console.error("Erro ao salvar:", error);
                        alert("Erro ao salvar: " + error.message);
                    });
            });
        });
    </script>

</x-app-layout>
