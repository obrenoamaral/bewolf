<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Perguntas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-no-repeat bg-cover bg-center h-screen m-auto" style="background-image: url('{{ asset('background.webp') }}');">
<!-- Tela de introdução -->
<div class="bg-black opacity-70 h-screen flex items-center justify-center" id="introScreen">
    <div class="max-w-4xl w-full p-8 text-start">
        <h1 class="text-2xl text-gray-100 font-bold mb-6 uppercase">Seja muito bem-vindo(a) ao Diagnóstico Empresarial BeWolf</h1>
        <p class="text-gray-100 mb-6">Quer ter uma visão clara da situação atual da sua empresa e identificar áreas de melhoria?</p>
        <p class="text-gray-100 mb-6">Este diagnóstico gratuito, com 35 perguntas de Múltipla Escolha, foi desenvolvido para te ajudar a obter um panorama completo do seu negócio. Responda com atenção e receba um relatório personalizado com recomendações estratégicas.</p>
        <p class="text-gray-100 mb-6">Tempo estimado para preenchimento: 5 minutos</p>
        <p class="text-gray-100 mb-6">Este formulário exige concentração. Certifique-se de estar focado antes de começar. Quando estiver preparado, clique em "Começar".</p>
        <button id="startButton" class="bg-gray-100 text-black px-4 py-2 hover:bg-gray-800 hover:text-white rounded">Começar</button>
    </div>
</div>

<!-- Formulário de perguntas -->
<div class="bg-black opacity-70 h-screen flex items-center justify-center hidden" id="questionScreen">
    <div class="max-w-4xl w-full p-8">
        <form id="questionForm" action="{{ route('form.submit') }}" method="POST" class="flex flex-col">
            @csrf
            <div id="questionContainer"></div>
            <div class="flex justify-between mt-4">
                <button type="button" id="backButton" class="w-32 bg-gray-100 text-black px-4 py-2 hover:bg-gray-800 hover:text-white rounded" disabled>Voltar</button>
                <button type="button" id="nextButton" class="w-32 bg-gray-100 text-black px-4 py-2 hover:bg-gray-800 hover:text-white rounded" disabled>Avançar</button>
            </div>
            <button type="submit" class="rounded bg-gray-100 text-black px-4 py-2 hover:bg-gray-800 hover:text-gray-100 w-40 mx-auto hidden" id="submitButton">
                Enviar Respostas
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const startButton = document.getElementById('startButton');
        const introScreen = document.getElementById('introScreen');
        const questionScreen = document.getElementById('questionScreen');
        const backButton = document.getElementById('backButton');
        const nextButton = document.getElementById('nextButton');
        const submitButton = document.getElementById('submitButton');
        const questionContainer = document.getElementById('questionContainer');
        const questions = @json($questions);
        let currentQuestionIndex = 0;

        // Função para mostrar a pergunta atual
        function showQuestion(index) {
            if (index < questions.length) {
                const question = questions[index];
                questionContainer.innerHTML = `
                        <div class="mb-6">
                            <p class="font-semibold mb-2 text-gray-100">${question.question}</p>
                            ${question.answers.map(answer => `
                                <label class="block mb-2 text-gray-100">
                                    <input type="radio" name="answers[${question.id}]" value="${answer.id}" required class="form-radio h-4 w-4 text-black border-2 border-gray-300 rounded-sm focus:outline-none focus:ring-2 focus:ring-blue-500 hover:bg-gray-200">
                                    ${answer.answer}
                                </label>
                            `).join('')}
                        </div>
                    `;
                // Exibe o botão de envio na última pergunta
                if (index === questions.length - 1) {
                    nextButton.classList.add('hidden');
                    submitButton.classList.remove('hidden');
                } else {
                    nextButton.classList.remove('hidden');
                    submitButton.classList.add('hidden');
                }
            }
        }

        // Função para avançar para a próxima pergunta
        function showNextQuestion() {
            if (currentQuestionIndex < questions.length - 1) {
                currentQuestionIndex++;
                showQuestion(currentQuestionIndex);
            }
            toggleButtons();
        }

        // Função para voltar para a pergunta anterior
        function showPreviousQuestion() {
            if (currentQuestionIndex > 0) {
                currentQuestionIndex--;
                showQuestion(currentQuestionIndex);
            }
            toggleButtons();
        }

        // Função para ativar ou desativar os botões de navegação
        function toggleButtons() {
            if (currentQuestionIndex === 0) {
                backButton.disabled = true;
            } else {
                backButton.disabled = false;
            }

            const isAnswered = document.querySelector(`input[name="answers[${questions[currentQuestionIndex].id}]"]:checked`);
            nextButton.disabled = !isAnswered;
        }

        // Quando o usuário clicar em "Começar", esconde a tela de introdução e mostra o formulário
        startButton.addEventListener('click', function () {
            introScreen.classList.add('hidden');
            questionScreen.classList.remove('hidden');
            showQuestion(currentQuestionIndex);
        });

        // Quando o usuário clicar em "Avançar"
        nextButton.addEventListener('click', function () {
            showNextQuestion();
        });

        // Quando o usuário clicar em "Voltar"
        backButton.addEventListener('click', function () {
            showPreviousQuestion();
        });

        // Adiciona o evento para detectar quando uma resposta é selecionada
        questionContainer.addEventListener('change', function () {
            toggleButtons();
        });

        // Inicializa com a primeira pergunta
        showQuestion(currentQuestionIndex);

        // Adiciona o evento para enviar a resposta quando o botão "Enviar Respostas" for clicado
        document.getElementById('questionForm').addEventListener('submit', function (e) {
            console.log('Formulário enviado');
        });
    });
</script>
</body>
</html>
