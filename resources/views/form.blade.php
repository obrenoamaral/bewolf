<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Perguntas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Estilos para o spinner */
        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-left-color: #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 5px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Estilos para os inputs de radio */
        .form-radio {
            @apply h-4 w-4 text-blue-500 border-2 border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-blue-500 hover:bg-gray-200;
        }
    </style>
</head>

<body class="bg-no-repeat bg-cover bg-center h-screen m-auto" style="background-image: url('{{ asset('background1.webp') }}');">

<div class="bg-black opacity-80 h-screen flex items-center justify-center" id="introScreen">
    <div class="max-w-4xl w-full p-8 text-center md:text-left">
        <h1 class="text-2xl md:text-2xl text-gray-100 font-bold mb-6 uppercase">Seja muito bem-vindo(a) ao Diagnóstico Empresarial BeWolf</h1>
        <p class="text-base md:text-md text-gray-100 mb-6">Quer ter uma visão clara da situação atual da sua empresa e identificar áreas de melhoria?</p>
        <p class="text-base md:text-md text-gray-100 mb-6">Este diagnóstico gratuito, com 30 perguntas, foi desenvolvido para te ajudar a obter um panorama completo do seu negócio. Responda com atenção e receba um relatório personalizado com recomendações estratégicas.</p>
        <p class="text-base md:text-md text-gray-100 mb-6">Tempo estimado para preenchimento: 5 minutos</p>
        <p class="text-base md:text-md text-gray-100 mb-6">Este formulário exige concentração. Certifique-se de estar focado antes de começar. Quando estiver preparado, clique em "Começar".</p>
        <button id="startButton" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition duration-300">Começar</button>
    </div>
</div>

<div class="bg-black opacity-80 h-screen flex items-center justify-center hidden transition-opacity duration-500" id="questionScreen">
    <div class="max-w-4xl w-full p-8">
        <form id="questionForm">
            @csrf
            <div id="questionContainer"></div>
            <div id="multipleChoiceContainer" class="hidden"></div>

            <div class="mt-6 flex justify-between">
                <button type="button" id="backButton" class="bg-gray-100 text-black px-4 py-2 hover:bg-gray-800 hover:text-white rounded disabled:opacity-50" disabled>Voltar</button>
                <button type="button" id="nextButton" class="bg-gray-100 text-black px-4 py-2 hover:bg-gray-800 hover:text-white rounded disabled:opacity-50" disabled>Próximo</button>
                <button type="button" id="submitButton" class="bg-gray-100 text-black px-4 py-2 hover:bg-gray-800 hover:text-white rounded hidden">Enviar</button>
            </div>
        </form>
    </div>
</div>

<div class="bg-black opacity-80 h-screen flex items-center justify-center hidden transition-opacity duration-500" id="clientInfoScreen">
    <div class="max-w-4xl w-full p-8">
        <div class="p-6 rounded-lg">
            <form id="clientInfoForm">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block font-medium text-gray-100 text-sm">Nome</label>
                    <input type="text" name="name" id="name" class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm bg-transparent text-gray-100 text-sm" required>
                </div>
                <div class="mb-4">
                    <label for="company" class="block font-medium text-gray-100 text-sm">Empresa</label>
                    <input type="text" name="company" id="company" class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm bg-transparent text-gray-100 text-sm" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block font-medium text-gray-100 text-sm">Email</label>
                    <input type="email" name="email" id="email" class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm bg-transparent text-gray-100 text-sm" required>
                </div>
                <div class="mb-4">
                    <label for="phone" class="block font-medium text-gray-100 text-sm">Telefone</label>
                    <input type="text" name="phone" id="phone" class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm bg-transparent text-gray-100 text-sm" required>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-gray-100 text-black px-4 py-2 hover:bg-gray-800 hover:text-white rounded">Enviar</button>
                    <span id="loadingIndicator" class="hidden ml-2 text-gray-100">
                        <div class="spinner"></div> Enviando...
                    </span>
                </div>
            </form>
        </div>
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
        const loadingIndicator = document.getElementById('loadingIndicator');

        const questions = @json($questions);
        const multipleChoiceQuestions = @json($multipleChoiceQuestions);
        const answers = {}; // Objeto para armazenar as respostas das perguntas normais
        const multipleChoiceAnswers = {}; // Objeto para armazenar as respostas de múltipla escolha


        let currentQuestionIndex = 0;
        let currentMultipleChoiceIndex = 0;
        let currentSection = 'questions'; // 'questions' ou 'multipleChoice'
        let questionCounter = 1;

        $(document).ready(function() {
            $('#phone').inputmask('(99) 99999-9999');
        });

        function showQuestion(index) {
            currentSection = 'questions';
            const question = questions[index];
            questionContainer.innerHTML = `
                <div class="mb-4 opacity-0 transition-opacity duration-500">
                    <label for="question-${question.id}" class="block font-medium text-gray-100">${questionCounter}. ${question.question}</label>
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
            questionContainer.classList.remove('hidden');
            multipleChoiceContainer.classList.add('hidden');
            toggleButtons();
        }

        function showMultipleChoiceQuestion(index) {
            currentSection = 'multipleChoice';
            const question = multipleChoiceQuestions[index];

            if (!question || !question.answers_multiple_choice || !Array.isArray(question.answers_multiple_choice)) {
                console.error('Respostas de múltipla escolha não definidas ou inválidas:', question);
                return;
            }

            multipleChoiceContainer.innerHTML = `
            <div class="mb-6 opacity-0 transition-opacity duration-500">
                <p class="font-semibold mb-2 text-gray-100">${questionCounter}. ${question.question_title}</p>
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

            multipleChoiceContainer.classList.remove('hidden');
            questionContainer.classList.add('hidden');
            toggleButtons();
        }

        function showNextQuestion() {
            if (currentSection === 'questions') {
                if (currentQuestionIndex < questions.length - 1) {
                    currentQuestionIndex++;
                    questionCounter++;
                    showQuestion(currentQuestionIndex);
                } else {
                    currentSection = 'multipleChoice';
                    currentMultipleChoiceIndex = 0;
                    questionCounter++;
                    showMultipleChoiceQuestion(currentMultipleChoiceIndex);
                }
            } else if (currentSection === 'multipleChoice') {
                if (currentMultipleChoiceIndex < multipleChoiceQuestions.length - 1) {
                    currentMultipleChoiceIndex++;
                    questionCounter++;
                    showMultipleChoiceQuestion(currentMultipleChoiceIndex);
                } else {
                    questionScreen.classList.add('hidden');
                    clientInfoScreen.classList.remove('hidden');
                }
            }
        }

        function showPreviousQuestion() {
            if (currentSection === 'multipleChoice') {
                if (currentMultipleChoiceIndex > 0) {
                    currentMultipleChoiceIndex--;
                    questionCounter--;
                    showMultipleChoiceQuestion(currentMultipleChoiceIndex);
                } else {
                    //Volta para ultima questao normal
                    currentSection = 'questions';
                    currentQuestionIndex = questions.length -1;
                    questionCounter--;
                    showQuestion(currentQuestionIndex);
                }
            } else if(currentSection === 'questions'){
                if (currentQuestionIndex > 0) {
                    currentQuestionIndex--;
                    questionCounter--;
                    showQuestion(currentQuestionIndex);
                }  else { //Adicionado
                    questionScreen.classList.add('hidden');
                    introScreen.classList.remove('hidden');
                }
            }
        }

        function toggleButtons() {
            // Botão Voltar
            if (currentSection === 'questions' && currentQuestionIndex === 0) {
                backButton.textContent = "Voltar para Boas-vindas";
                backButton.disabled = false;
            } else {
                backButton.textContent = "Voltar";
                backButton.disabled =  !(currentSection === 'questions' && currentQuestionIndex === 0);
            }

            // Botão Próximo / Enviar
            if (currentSection === 'questions') {
                const currentQuestionId = questions[currentQuestionIndex].id;
                const isAnswered = document.querySelector(`input[name="answers[${currentQuestionId}]"]:checked`);
                nextButton.disabled = !isAnswered;
                nextButton.classList.remove('hidden');
                submitButton.classList.add('hidden');

            } else if (currentSection === 'multipleChoice') {
                const currentMultipleChoiceId = multipleChoiceQuestions[currentMultipleChoiceIndex].id;
                const isAnswered = document.querySelector(`input[name="multiple_choice_answers[${currentMultipleChoiceId}]"]:checked`);
                nextButton.disabled = !isAnswered;
                if(currentMultipleChoiceIndex < multipleChoiceQuestions.length -1){
                    nextButton.classList.remove('hidden');
                    submitButton.classList.add('hidden');
                } else {
                    nextButton.classList.add('hidden');
                    submitButton.classList.remove('hidden');
                }
            }
        }


        startButton.addEventListener('click', function () {
            introScreen.classList.add('hidden');
            questionScreen.classList.remove('hidden');
            showQuestion(currentQuestionIndex);
        });

        nextButton.addEventListener('click', showNextQuestion);
        backButton.addEventListener('click', showPreviousQuestion);

        // *** RESTAURADO: Event listeners para coletar as respostas ***
        questionContainer.addEventListener('change', function (event) {
            const questionId = event.target.name.match(/\[(\d+)\]/)[1];  // Extrai o ID da pergunta
            if (!answers[questionId]) {
                answers[questionId] = []; // Inicializa o array se for a primeira resposta
            }
            if (!answers[questionId].includes(event.target.value)) {
                answers[questionId].push(event.target.value);
            }
            toggleButtons(); // Atualiza o estado dos botões
        });

        multipleChoiceContainer.addEventListener('change', function (event) {
            const questionId = event.target.name.match(/\[(\d+)\]/)[1]; // Extrai o ID da pergunta
            multipleChoiceAnswers[questionId] = event.target.value; // Armazena a resposta (sobrescreve se já existir)
            toggleButtons(); // Atualiza o estado dos botões
        });
        // *** FIM DA SEÇÃO RESTAURADA ***

        submitButton.addEventListener('click', function () {
            questionScreen.classList.add('hidden');
            clientInfoScreen.classList.remove('hidden');
        });


        clientInfoForm.addEventListener('submit', function (e) {
            e.preventDefault();

            loadingIndicator.classList.remove('hidden');
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

            const formData = new FormData(this);

            // *** Adiciona as respostas ao FormData ***
            Object.keys(answers).forEach(questionId => {
                answers[questionId].forEach(answerId => { // Corrigido: Itera sobre os IDs das respostas
                    formData.append(`answers[${questionId}][]`, answerId);
                });
            });

            Object.keys(multipleChoiceAnswers).forEach(questionId => {
                formData.append(`multiple_choice_answers[${questionId}]`, multipleChoiceAnswers[questionId]);
            });
            // *** Fim da adição ao FormData ***


            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/form/submit-info', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
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
                    alert('Erro ao enviar o formulário.  Por favor, tente novamente.');
                })
                .finally(() => {
                    loadingIndicator.classList.add('hidden');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                });
        });

        toggleButtons(); //Chamada inicial
    });

</script>
