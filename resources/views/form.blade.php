<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Perguntas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-no-repeat bg-cover bg-center h-screen m-auto" style="background-image: url('{{ asset('background1.webp') }}');">

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

<div class="bg-black opacity-70 h-screen flex items-center justify-center hidden transition-opacity" id="questionScreen">
    <div class="max-w-4xl w-full p-8">
        <form id="questionForm">
            @csrf
            <!-- Questões Simples -->
            <div id="questionContainer"></div>

            <!-- Questões de Múltipla Escolha -->
            <div id="multipleChoiceContainer" class="hidden"></div>

            <div class="mt-6 flex justify-between">
                <button type="button" id="backButton" class="bg-gray-100 text-black px-4 py-2 hover:bg-gray-800 hover:text-white rounded disabled:opacity-50" disabled>Voltar</button>
                <button type="button" id="nextButton" class="bg-gray-100 text-black px-4 py-2 hover:bg-gray-800 hover:text-white rounded">Próximo</button>
                <button type="button" id="submitButton" class="bg-gray-100 text-black px-4 py-2 hover:bg-gray-800 hover:text-white rounded hidden">Enviar</button>
            </div>
        </form>
    </div>
</div>
<div class="bg-black opacity-70 h-screen flex items-center justify-center hidden" id="clientInfoScreen">
    <div class="max-w-4xl w-full p-8">
        <form id="clientInfoForm" >
            @csrf
            <div class="mb-4">
                <label for="name" class="block font-medium text-gray-100">Nome</label>
                <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div class="mb-4">
                <label for="company" class="block font-medium text-gray-100">Empresa</label>
                <input type="text" name="company" id="company" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block font-medium text-gray-100">Email</label>
                <input type="email" name="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div class="mb-4">
                <label for="phone" class="block font-medium text-gray-100">Telefone</label>
                <input type="text" name="phone" id="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div class="mt-6">
                <button type="submit" class="bg-gray-100 text-black px-4 py-2 hover:bg-gray-800 hover:text-white rounded">Enviar</button>
            </div>
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
        const clientInfoScreen = document.getElementById('clientInfoScreen');
        const backButton = document.getElementById('backButton');
        const nextButton = document.getElementById('nextButton');
        const submitButton = document.getElementById('submitButton');
        const questionContainer = document.getElementById('questionContainer');
        const multipleChoiceContainer = document.getElementById('multipleChoiceContainer');
        const clientInfoForm = document.getElementById('clientInfoForm');

        const questions = @json($questions);
        const multipleChoiceQuestions = @json($multipleChoiceQuestions);

        let currentQuestionIndex = 0;
        let currentMultipleChoiceIndex = 0;

        function showQuestion(index) {
            const question = questions[index];
            questionContainer.innerHTML = `
            <div class="mb-4 opacity-0 transition-opacity duration-500">
                <label for="question-${question.id}" class="block font-medium text-gray-100">${question.question}</label>
                ${question.answers.map(answer => `
                    <div class="flex items-center">
                        <input type="radio" name="answers[${question.id}]" id="answer-${answer.id}" value="${answer.id}" class="mr-2" required>
                        <label for="answer-${answer.id}" class="text-gray-100">${answer.answer}</label>
                    </div>
                `).join('')}
            </div>
        `;

            const questionElement = questionContainer.querySelector('div');
            setTimeout(() => questionElement.classList.add('opacity-100'), 10);
        }

        function showMultipleChoiceQuestion(index) {
            const question = multipleChoiceQuestions[index];

            if (!question.answers_multiple_choice || !Array.isArray(question.answers_multiple_choice)) {
                console.error('Respostas de múltipla escolha não definidas ou inválidas:', question);
                return;
            }

            multipleChoiceContainer.innerHTML = `
            <div class="mb-6 opacity-0 transition-opacity duration-500">
                <p class="font-semibold mb-2 text-gray-100">${question.question_title}</p>
                ${question.answers_multiple_choice.map(answer => `
                    <label class="block mb-2 text-gray-100">
                        <input type="radio" name="multiple_choice_answers[${question.id}]" value="${answer.id}" class="form-radio h-4 w-4 text-black border-2 border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-blue-500 hover:bg-gray-200">
                        ${answer.answer}
                    </label>
                `).join('')}
            </div>
        `;

            const multipleChoiceElement = multipleChoiceContainer.querySelector('div');
            setTimeout(() => multipleChoiceElement.classList.add('opacity-100'), 10);

            // Verifica se é a última questão de múltipla escolha
            if (index === multipleChoiceQuestions.length - 1) {
                nextButton.classList.add('hidden');
                submitButton.classList.remove('hidden');
            } else {
                nextButton.classList.remove('hidden');
                submitButton.classList.add('hidden');
            }
        }

        function showNextQuestion() {
            if (currentQuestionIndex < questions.length - 1) {
                currentQuestionIndex++;
                questionContainer.classList.add('opacity-0');
                setTimeout(() => {
                    showQuestion(currentQuestionIndex);
                    questionContainer.classList.remove('opacity-0');
                }, 500);
            } else {
                questionContainer.classList.add('opacity-0');
                setTimeout(() => {
                    multipleChoiceContainer.classList.remove('hidden');

                    // Incrementa o índice ANTES de exibir a próxima pergunta de múltipla escolha
                    currentMultipleChoiceIndex++;  // Esta é a linha crucial que faltava

                    showMultipleChoiceQuestion(currentMultipleChoiceIndex -1 ); // Passa o índice correto

                    questionContainer.classList.add('hidden');
                    multipleChoiceContainer.classList.add('opacity-0');
                    setTimeout(() => multipleChoiceContainer.classList.remove('opacity-0'), 10);
                }, 500);
            }
        }

        function showPreviousQuestion() {
            if (currentQuestionIndex > 0) {
                currentQuestionIndex--;
                questionContainer.classList.add('opacity-0');
                setTimeout(() => {
                    showQuestion(currentQuestionIndex);
                    questionContainer.classList.remove('opacity-0');
                }, 500);
            } else if (currentMultipleChoiceIndex > 0) {
                multipleChoiceContainer.classList.add('opacity-0');
                setTimeout(() => {
                    multipleChoiceContainer.classList.add('hidden');
                    questionContainer.classList.remove('hidden');
                    showQuestion(currentQuestionIndex);
                    multipleChoiceContainer.classList.remove('opacity-0');
                }, 500);
                currentMultipleChoiceIndex--;
            }
        }

        function toggleButtons() {
            if (currentQuestionIndex === 0 && currentMultipleChoiceIndex === 0) {
                backButton.disabled = true;
            } else {
                backButton.disabled = false;
            }

            if (currentQuestionIndex < questions.length) {
                const currentQuestionId = questions[currentQuestionIndex].id;
                const isAnswered = document.querySelector(`input[name="answers[${currentQuestionId}]"]:checked`);
                nextButton.disabled = !isAnswered;
            } else {
                // Nova lógica para radio buttons: verifica se um radio button está marcado na pergunta atual
                const currentMultipleChoiceId = multipleChoiceQuestions[currentMultipleChoiceIndex].id;
                const isAnswered = document.querySelector(`input[name="multiple_choice_answers[${currentMultipleChoiceId}]"]:checked`);
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

        // Comportamento do botão "Enviar"
        submitButton.addEventListener('click', function () {
            // Oculta a tela de questões e exibe o formulário de informações do cliente
            questionScreen.classList.add('hidden');
            clientInfoScreen.classList.remove('hidden');
        });

        clientInfoForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            // Coleta respostas de múltipla escolha agrupadas por pergunta
            const multipleChoiceAnswers = {};
            document.querySelectorAll('input[name^="multiple_choice_answers"]:checked').forEach(input => {
                const questionId = input.name.match(/\[(\d+)\]/)[1]; // Extrai o ID da pergunta
                if (!multipleChoiceAnswers[questionId]) {
                    multipleChoiceAnswers[questionId] = [];
                }
                multipleChoiceAnswers[questionId].push(input.value);
            });

            // Adiciona as respostas agrupadas ao FormData
            for (const [questionId, answers] of Object.entries(multipleChoiceAnswers)) {
                formData.append(`multiple_choice_answers[${questionId}]`, answers.join(','));
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Envia os dados
            fetch('/form/submit-info', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken.content
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '/thankyou';
                    } else {
                        alert(data.message || 'Erro ao enviar o formulário.');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao enviar o formulário. Por favor, tente novamente.');
                });
        });
    });

</script>
