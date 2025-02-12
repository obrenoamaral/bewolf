<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Perguntas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-no-repeat bg-cover bg-center h-screen m-auto" style="background-image: url('{{ asset('background.webp') }}');">

<div class="bg-black opacity-70 h-screen flex items-center justify-center" id="introScreen">
    <div class="max-w-4xl w-full p-8 text-start">
        <h1 class="text-2xl text-gray-100 font-bold mb-6 uppercase">Seja muito bem-vindo(a) ao Diagnóstico Empresarial BeWolf</h1>
        <p class="text-gray-100 mb-6">Quer ter uma visão clara da situação atual da sua empresa e identificar áreas de melhoria?</p>
        <p class="text-gray-100 mb-6">Este diagnóstico gratuito, com 35 perguntas, foi desenvolvido para te ajudar a obter um panorama completo do seu negócio. Responda com atenção e receba um relatório personalizado com recomendações estratégicas.</p>
        <p class="text-gray-100 mb-6">Tempo estimado para preenchimento: 5 minutos</p>
        <p class="text-gray-100 mb-6">Este formulário exige concentração. Certifique-se de estar focado antes de começar. Quando estiver preparado, clique em "Começar".</p>
        <button id="startButton" class="bg-gray-100 text-black px-4 py-2 hover:bg-gray-800 hover:text-white rounded">Começar</button>
    </div>
</div>

<div class="bg-black opacity-70 h-screen flex items-center justify-center hidden" id="questionScreen">
    <div class="max-w-4xl w-full p-8">
        <form action="{{ route('form.submit') }}" method="POST">
            @csrf

            <h2>Questões Simples</h2>
            @foreach ($questions as $question)
                <div class="mb-4">
                    <label for="question-{{ $question->id }}" class="block font-medium text-gray-100">{{ $question->question }}</label>
                    @foreach ($question->answers as $answer)
                        <div class="flex items-center">
                            <input type="radio" name="answers[{{ $question->id }}]" id="answer-{{ $answer->id }}" value="{{ $answer->id }}" class="mr-2" required> {{-- Adicionado 'required' --}}
                            <label for="answer-{{ $answer->id }}" class="text-gray-100">{{ $answer->answer }}</label>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <h2>Questões de Múltipla Escolha</h2>
            @foreach ($multipleChoiceQuestions as $question)
                <div class="mb-4">
                    <label for="question-{{ $question->id }}" class="text-gray-100">{{ $question->question_title }}</label>
                    @foreach ($question->answersMultipleChoices as $answer) {{-- Nome do relacionamento corrigido --}}
                    <div class="flex items-center">
                        <input type="checkbox" name="multiple_choice_answers[{{ $question->id }}][]" value="{{ $answer->id }}">
                        <label for="answer-{{ $answer->id }}" class="text-gray-100">{{ $answer->answer }}</label>
                    </div>
                    @endforeach
                </div>
            @endforeach

            <button type="submit" class="bg-gray-100 text-black px-4 py-2 hover:bg-gray-800 hover:text-white rounded">Enviar</button>
        </form>
    </div>
</div>

</body>
</html>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const startButton = document.getElementById('startButton');
        const introScreen = document.getElementById('introScreen');
        const questionScreen = document.getElementById('questionScreen');
        const backButton = document.getElementById('backButton');
        const nextButton = document.getElementById('nextButton');
        const submitButton = document.getElementById('submitButton');
        const questionContainer = document.getElementById('questionContainer');
        const multipleChoiceContainer = document.getElementById('multipleChoiceContainer');
        const questions = @json($questions);
        const multipleChoiceQuestions = @json($multipleChoiceQuestions);
        let currentQuestionIndex = 0;
        let currentMultipleChoiceIndex = 0;

        function showQuestion(index) {
            // ... (sem alterações)
        }

        function showMultipleChoiceQuestion(index) {
            if (index < multipleChoiceQuestions.length) {
                const question = multipleChoiceQuestions[index];
                multipleChoiceContainer.innerHTML = `
                <div class="mb-6">
                    <p class="font-semibold mb-2 text-gray-100">${question.question_title}</p>
                    ${question.answers_multiple_choice.map(answer => `
                        <label class="block mb-2 text-gray-100">
                            <input type="radio" name="multiple_choice_answers[${question.id}]" value="${answer.id}" required class="form-radio h-4 w-4 text-black border-2 border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-blue-500 hover:bg-gray-200">
                            ${answer.answer}
                        </label>
                    `).join('')}
                </div>
            `;

                if (index === multipleChoiceQuestions.length - 1) {
                    nextButton.classList.add('hidden');
                    submitButton.classList.remove('hidden');
                } else {
                    nextButton.classList.remove('hidden');
                    submitButton.classList.add('hidden');
                }
            }
        }


        function showNextQuestion() {
            if (currentQuestionIndex < questions.length - 1) {
                currentQuestionIndex++;
                showQuestion(currentQuestionIndex);
            } else {
                questionContainer.classList.add('hidden'); // Esconde as perguntas Sim/Não
                multipleChoiceContainer.classList.remove('hidden');
                showMultipleChoiceQuestion(currentMultipleChoiceIndex);
            }
            toggleButtons();
        }

        function showPreviousQuestion() {
            if (currentQuestionIndex > 0) {
                currentQuestionIndex--;
                showQuestion(currentQuestionIndex);
            } else if (currentMultipleChoiceIndex > 0) {
                multipleChoiceContainer.classList.add('hidden');
                questionContainer.classList.remove('hidden');
                showMultipleChoiceQuestion(currentMultipleChoiceIndex - 1); // Exibe a pergunta múltipla escolha anterior
                currentMultipleChoiceIndex--; // Decrementa o índice de múltipla escolha
            }
            toggleButtons();
        }


        function toggleButtons() {
            if (currentQuestionIndex === 0 && currentMultipleChoiceIndex === 0) {
                backButton.disabled = true;
            } else {
                backButton.disabled = false;
            }

            if (currentQuestionIndex < questions.length) {
                const isAnswered = document.querySelector(`input[name="answers[${questions[currentQuestionIndex].id}]"]:checked`);
                nextButton.disabled = !isAnswered;
            } else {
                const isAnswered = document.querySelector(`input[name="multiple_choice_answers[${multipleChoiceQuestions[currentMultipleChoiceIndex].id}]"]:checked`);
                nextButton.disabled = !isAnswered;
            }
        }

        startButton.addEventListener('click', function () {
            introScreen.classList.add('hidden');
            questionScreen.classList.remove('hidden');
            showQuestion(currentQuestionIndex);
        });

        nextButton.addEventListener('click', function () {
            showNextQuestion();
        });

        backButton.addEventListener('click', function () {
            showPreviousQuestion();
        });

        questionContainer.addEventListener('change', function () {
            toggleButtons();
        });

        multipleChoiceContainer.addEventListener('change', function () {
            toggleButtons();
        });

        document.getElementById('questionForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(document.getElementById('questionForm'));

            fetch(document.getElementById('questionForm').action, {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (response.ok) {
                        alert('Respostas enviadas com sucesso!');
                        // Redirecionar para outra página ou fazer algo mais
                    } else {
                        alert('Erro ao enviar as respostas. Tente novamente.');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Ocorreu um erro ao enviar o formulário. Tente novamente mais tarde.');
                });
        });
    });
</script>
