<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Diagnóstico</title>
    <style>
        @page {
            size: A4;
            margin: 20mm 5mm 15mm 5mm; /* Margens superior, direita, inferior, esquerda */
        }
        body {
            font-family: sans-serif; /* Fonte mais segura para PDFs */
            font-size: 12pt;
            line-height: 1.4; /* Espaçamento entre linhas */
        }
        .page-break {
            page-break-after: always;
        }
        .container {
            width: 100%;
            max-width: 100%; /* Garante que o conteúdo não ultrapasse as margens */
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1pt solid black;
            padding: 8pt;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        .mt-2 { margin-top: 0.5rem; }
        .mt-4 { margin-top: 1rem; }
        .mt-6 { margin-top: 1.5rem; }
        .mt-8 { margin-top: 2rem; }
        .mt-10 { margin-top: 2.5rem; }
        .mb-20 { margin-bottom: 5rem;}
        .mb-40 { margin-bottom: 10rem;}
        .mx-auto { margin-left: auto; margin-right: auto; }
        .w-96 { width: 24rem;}
        .w-2-3 { width: 66.666667%; }
        .w-1-2 { width: 50%; }
        .w-1-3 { width: 33.333333%;}
        .p-1 { padding: 0.25rem; }
        .p-2 { padding: 0.5rem; }
        .p-4 { padding: 1rem; }
        .p-8 { padding: 2rem; }
        .text-white { color: white; }
        .text-black { color: black; }
        .text-lg { font-size: 1.125rem; /* 18px */}
        .text-md { font-size: 1rem; /* 16px */}
        .text-2xl { font-size: 1.5rem; /* 24px */}
        .text-base { font-size: 1rem;}
        .text-gray-800 { color: #1f2937; }
        .text-gray-900 { color: #111827; }
        .text-gray-700 { color: #374151; }
        .bg-black { background-color: black;}
        .bg-gray-600 { background-color: #4b5563; }
        .bg-green-500 { background-color: #22c55e; }
        .bg-red-500 { background-color: #ef4444; }
        .bg-blue-500 { background-color: #3b82f6; }
        .border { border: 1pt solid black;}
        .border-gray-300 { border: 1pt solid #d1d5db;}
        .border-gray-600 { border: 1pt solid #4b5563;}
        .rounded-t { border-top-left-radius: 0.25rem; border-top-right-radius: 0.25rem; }
        .font-semibold { font-weight: 600; }
        .my-5 { margin-top: 1.25rem; margin-bottom: 1.25rem; }
        .space-y-2 > :not([hidden]) ~ :not([hidden]) { --tw-space-y-reverse: 0; margin-top: calc(0.5rem * calc(1 - var(--tw-space-y-reverse))); margin-bottom: calc(0.5rem * var(--tw-space-y-reverse)); }
        .flex { display: flex;}
        .items-center { align-items: center;}
        .justify-center { justify-content: center;}
        .flex-col { flex-direction: column;}


    </style>
</head>
<body style="margin: 15mm 5mm 15mm 5mm;">

<div class="container page-break" style="padding: 8pt; width: 100%; box-sizing: border-box;">
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;">
        <header style="width: 100%; text-align: center;">
            <img src="{{ public_path('logo-preto.png') }}" alt="Logotipo BeWolf" style="width: 24rem; margin-bottom: 10rem; display: block; margin-left: auto; margin-right: auto;">
            <h1 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 5rem; ">RELATÓRIO DE DIAGNÓSTICO EMPRESARIAL E ESTRATÉGICO</h1>
            <p style="font-size: 1rem; margin-top: 0.5rem; margin-bottom: 10rem; line-height: 1.2;">Esse relatório foi construído com base nas suas respostas.<br>Qualquer dúvida, estamos à disposição para ajudá-lo.</p>
            <p style="font-size: 1rem; font-weight: 600; margin-top: 0.5rem; margin-bottom: 1rem;">BeWolf Consultoria Empresarial</p>
            <p style=" font-size: 1rem; font-weight: 600; margin-top: 0.5rem;">www.bwolf.com.br</p>
        </header>
    </div>
</div>

<main class="container" >
    <section class="table-section">
        <div style="background-color: black; color: white; text-align: center; margin-top: 1.5rem; border-top-left-radius: 0.25rem; border-top-right-radius: 0.25rem;">
            <h2 style="font-size: 1.125rem; font-weight: 600; margin-top: 1.25rem; margin-bottom: 1.25rem;">MAPEAMENTO DE DIAGNÓSTICO EMPRESARIAL</h2>
        </div>
        <table role="table">
            <tbody>
            @foreach($orderedPoints as $data)
                <tr style="border: 1pt solid black;">
                    <td style="padding: 0.5rem; width: 66.666667%;">{{ $data['question'] }}</td>
                    <td style="padding: 0.5rem; width: 33.333333%;
                    @if(!empty($data['answer']))
                        background-color: #4b5563; color: white;
                    @else
                        background-color: #f2f2f2; /* Ou qualquer outra cor */
                    @endif
                    text-align: center;">
                        {{ $data['answer'] ?: 'N/A' }}
                    </td>
            @endforeach
            </tbody>
        </table>
    </section>

    <section class="diagnosis-result page-break">
        <?php
        $bgColor = 'bg-green-500'; // Tailwind class - will be ignored
        if (strpos($diagnosisResult['name'], 'Empreendedor Extintor') !== false) {
            $bgColor = 'bg-red-500';  // Tailwind class - will be ignored
        } elseif (strpos($diagnosisResult['name'], 'Empreendedor Sobrecarregado') !== false) {
            $bgColor = 'bg-blue-500';  // Tailwind class - will be ignored
        }

        // Convert Tailwind classes to inline styles
        $bgColorStyle = 'background-color: #22c55e;'; // Default green
        if ($bgColor === 'bg-red-500') {
            $bgColorStyle = 'background-color: #ef4444;'; // Red
        } elseif ($bgColor === 'bg-blue-500') {
            $bgColorStyle = 'background-color: #3b82f6;'; // Blue
        }
        ?>
        <div style="border: 0.5pt solid #4b5563; margin-top: 0.4rem;">
            <div style="font-size: 1rem; font-weight: bold; color: white; text-align: center; {{ $bgColorStyle }}">
                <p style="margin: 0; padding: 0.2rem;">{{ $diagnosisResult['name'] }}</p> </div>
            <div style="padding: 0.4rem; color: black; word-wrap: break-word;"> <p style="margin:0;">{{ $diagnosisResult['description'] }}</p> </div>
        </div>
    </section>

    <section class="analysis page-break" style="margin-top: 2.5rem;">
        <h3 style="text-align: center; margin-top: 1.25rem; margin-bottom: 1.25rem;">ANÁLISE DE POTENCIAIS PONTOS FORTES E OPORTUNIDADES DE DESENVOLVIMENTO</h3>
        <table role="table">
            <thead>
            <tr>
                <th style="background-color: #22c55e; color: white; width: 50%; padding: 0.25rem; border: 1pt solid #4b5563;">PONTOS FORTES</th>
                <th style="background-color: #ef4444; color: white; width: 50%; padding: 0.25rem; border: 1pt solid #4b5563;">PONTOS PARA DESENVOLVER</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($orderedPoints as $point)
                <tr style="border: 1pt solid #d1d5db;">
                    @if ($point['strength_weakness'] === 'strong')
                        <td style="padding: 0.5rem; border: 1pt solid #d1d5db;">
                            <strong style="font-weight: bold;">{{ $point['strength_weakness_title'] }}</strong>
                        </td>
                        <td style="padding: 0.5rem; border: 1pt solid #d1d5db;"></td> {{-- Célula vazia para pontos fracos --}}
                    @else
                        <td style="padding: 0.5rem; border: 1pt solid #d1d5db;"></td> {{-- Célula vazia para pontos fortes --}}
                        <td style="padding: 0.5rem; border: 1pt solid #d1d5db;">
                            <strong style="font-weight: bold;">{{ $point['strength_weakness_title'] }}</strong>
                        </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>

    <section class="multiple-choice-answers page-break" style="width: 100%;">
        <h3 style="font-weight: bold; text-align: center; margin-top: 1.25rem; margin-bottom: 1.25rem;">ANÁLISES GERAIS DA EMPRESA E EMPREENDEDOR</h3>
        @if ($multipleChoiceAnswers->isNotEmpty())
            @foreach ($multipleChoiceAnswers as $answer)
                <p style="font-weight: bold;">{{ $answer->questionMultipleChoice->solution_title }}</p>
                <p> {{ $answer->answerMultipleChoice->diagnosis }}</p>
            @endforeach
        @endif
    </section>

    <section class="diagnosis-details page-break">
        <h1 style="margin-top: 2rem; font-size: 1.125rem; font-weight: bold; text-align: center;">DETALHAMENTO DO DIAGNÓSTICO</h1>
        <?php $questionCounter = 1; ?>
        @foreach($orderedPoints as $data)
            <div style="margin-top: 1.5rem; padding: 1rem;">
                <h2 style="font-size: 1rem; font-weight: 600;">{{ $questionCounter }}. {{ $data['diagnosis_title'] }}</h2>
                <p style="margin-top: 0.5rem;"><span style="font-weight: bold;">Diagnóstico:</span></p>
                <p style="word-wrap: break-word;">{{ $data['diagnosis'] }}</p>
                <p style="margin-top: 0.5rem;"><span style="font-weight: bold;">Solução:</span></p>
                <p style="word-wrap: break-word;">{{ $data['solution'] }}</p>
            </div>
                <?php $questionCounter++; ?>
        @endforeach
    </section>
</main>

<footer class="container page-break">
    <div style="display:flex; flex-direction: column; items-center; justify-content: center; height: 100vh;">
        <p style="font-size: 1.125rem; font-weight: 600; color: #1f2937; text-align: center; margin-bottom: 20pt">
            Caso queira potencializar os resultados da sua empresa e alcançar seus objetivos de forma mais rápida e mais preparada,
            acesse os links abaixo:
        </p>
        <div style="margin-top: 1rem; ">
            <p style="font-weight: bold;" class="text-center">Indique Empresários para participar da Reunião Estratégica GRATUITA</p>
        </div>
        <p style="margin-top: 1rem; color: #374151;" class="text-center">Ficaremos sempre à sua disposição.</p>
        <p style="margin-top: 0.5rem; color: #111827; font-weight: bold;" class="text-center">BeWolf Consultoria Empresarial</p>
    </div>
</footer>

</body>
</html>
