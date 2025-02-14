<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Diagnóstico</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 font-sans">

<div class="container mx-auto p-8 h-full">
    <div class="container mx-auto p-8 h-full">
        <div class="flex flex-col items-center justify-center h-screen">
            <header class="text-center w-full">
                <img src="{{ public_path('logo-preto.png') }}" alt="Logotipo BeWolf" class="w-96 mb-40 mx-auto">
                <h1 class="text-2xl font-bold mb-20">RELATÓRIO DE DIAGNÓSTICO EMPRESARIAL E ESTRATÉGICO</h1>
                <p class="text-base mt-2 mb-40">Esse relatório foi construído com base nas suas respostas.<br>Qualquer dúvida,
                    estamos à disposição para ajudá-lo.</p>
                <p class="text-base font-semibold mt-2 mb-4">BeWolf Consultoria Empresarial</p>
                <p class="text-base font-semibold mt-2">www.bwolf.com.br</p>
            </header>
        </div>
    </div>
</div>

<main class="container">
    <section class="table-section page-break" >
        <div class="bg-black text-white text-center mt-6 rounded-t">
            <h2 class="text-lg font-semibold my-5">MAPEAMENTO DE DIAGNÓSTICO EMPRESARIAL</h2>
        </div>
        <table class="w-full border-collapse border border-black" role="table">
            <tbody>
            @foreach($reportData as $data)
                <tr class="border border-black">
                    <td class="p-2 w-2/3">{{ $data['question'] }}</td>
                    <td class="p-2 w-1/3 bg-gray-600 text-white text-center">{{ $data['answer'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>

    <section class="diagnosis-result">
        <?php
        $bgColor = 'bg-green-500';
        if (strpos($diagnosisResult['name'], 'Empreendedor Extintor') !== false) {
            $bgColor = 'bg-red-500';
        } elseif (strpos($diagnosisResult['name'], 'Empreendedor Sobrecarregado') !== false) {
            $bgColor = 'bg-blue-500';
        }
        ?>
        <div class="border border-gray-600 mt-2">
            <div class="text-lg font-bold text-white text-center {{ $bgColor }}">
                <p>{{ $diagnosisResult['name'] }}</p>
            </div>
            <div class="p-2 text-black">
                <p>{{ $diagnosisResult['description'] }}</p>
            </div>
        </div>
    </section>

    <section class="analysis mt-10 "> @pageBreak
        <h3 class="text-center my-5">ANÁLISE DE POTENCIAIS PONTOS FORTES E OPORTUNIDADES DE DESENVOLVIMENTO</h3>
        <table class="w-full border-collapse border border-gray-600" role="table">
            <thead>
            <tr>
                <th class="bg-green-500 text-white w-1/2 p-1 border border-gray-600">PONTOS FORTES</th>
                <th class="bg-red-500 text-white w-1/2 p-1 border border-gray-600">PONTOS PARA DESENVOLVER</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($strongPoints as $point)
                <tr class="border border-gray-300">
                    <td class="p-2 border border-gray-300">
                        <strong>{{ $point['strength_weakness_title'] }}</strong>
                    </td>
                    <td class="p-2 border border-gray-300"></td>
                </tr>
            @endforeach
            @foreach ($weakPoints as $point)
                <tr class="border border-gray-300">
                    <td class="p-2 border border-gray-300"></td>
                    <td class="p-2 border border-gray-300">
                        <strong>{{ $point['strength_weakness_title'] }}</strong>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>

    <section class="multiple-choice-answers w-full">@pageBreak
        <h3 class="font-bold text-center my-5">ANÁLISES GERAIS DA EMPRESA E EMPREENDEDOR</h3>
        @if ($multipleChoiceAnswers->isNotEmpty())
            @foreach ($multipleChoiceAnswers as $answer)
                <p class="font-bold">{{ $answer->questionMultipleChoice->question_title }}</p>
                <p> {{ $answer->answerMultipleChoice->answer }}</p>
            @endforeach
        @endif
    </section>

    <section class="diagnosis-details" >@pageBreak
        <h1 class="mt-8 text-lg font-bold text-center">Detalhamento do Diagnóstico</h1>
        @foreach($reportData as $data)
            <div class="mt-6 p-4">
                <h2 class="text-md font-semibold">{{ $data['diagnosis_title'] }}</h2>
                <p class="mt-2"><span class="font-bold">Diagnóstico:</span></p>
                <p>{{ $data['diagnosis'] }}</p>
                <p class="mt-2"><span class="font-bold">Solução:</span></p>
                <p>{{ $data['solution'] }}</p>
            </div>
        @endforeach
    </section>
</main>

<footer class="container h-screen" >@pageBreak
    <div class="flex flex-col items-center justify-center h-screen">
        <p class="text-lg font-semibold text-gray-800 text-center">
            Caso queira potencializar os resultados da sua empresa e alcançar seus objetivos de forma mais rápida e mais preparada,
            acesse os links abaixo:
        </p>
        <div class="mt-4 space-y-2">
            <p class="font-bold">Indique Empresários para participar da Reunião Estratégica GRATUITA</p>
        </div>
        <p class="mt-4 text-gray-700">Ficaremos sempre à sua disposição.</p>
        <p class="mt-2 text-gray-900 font-bold">BeWolf Consultoria Empresarial</p>
    </div>
</footer>

</body>
</html>
