<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Diagnóstico</title>
    <style>
        /* (Seus estilos - Sem grandes alterações) */
        @page {
            size: A4;
            margin: 20mm 5mm 15mm 5mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.2;
        }
        .page-break {
            page-break-after: always;
        }
        .container {
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1pt solid black;
            padding: 8pt;
            text-align: left;
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
        .mb-20 { margin-bottom: 5rem;}
        .mb-40 { margin-bottom: 10rem;}
        .mx-auto { margin-left: auto; margin-right: auto; }
        .w-2-3 { width: 66.666667%; }
        .w-1-2 { width: 50%; }
        .w-1-3 { width: 33.333333%;}
        .text-white { color: white; }
        .bg-green-500 { background-color: #22c55e; }
        .bg-red-500 { background-color: #ef4444; }
        .bg-blue-500 { background-color: #3b82f6; }
    </style>
</head>
<body style="margin: 15mm 5mm 15mm 5mm;">

<div class="container page-break" style="padding: 8pt;">
    {{-- (Cabeçalho e primeira tabela - Sem alterações) --}}
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

<main class="container">
    <section class="table-section">
        <table role="table">
            <thead>
            <tr>
                <th colspan="2" style="text-align: center; border: 1px solid black; border-bottom: 0px; padding-bottom: 1px; font-size: 1em; font-weight: bold;">MAPEAMENTO DE DIAGNÓSTICO EMPRESARIAL</th>
            </tr>
            </thead>
            <tbody>
            {{-- Um único loop para todas as respostas --}}
            @foreach($allPoints as $data)
                <tr style="border: 1pt solid black;">
                    <td style="padding: 0.5rem; width: 66.666667%;">{{ $data['question'] }}</td>
                    <td style="padding: 0.5rem; width: 33.333333%;
                        @if(!empty($data['answer']))
                            background-color: #4b5563; color: white;
                        @else
                            background-color: #f2f2f2; /* Cor para N/A */
                        @endif
                        text-align: center;">
                        {{ $data['answer'] ?: 'N/A' }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>

    <section class="diagnosis-result page-break">
        <?php
        // Lógica para a cor de fundo (mantida)
        $bgColorStyle = 'background-color: #22c55e;'; // Default green
        if (strpos($diagnosisResult['name'], 'Empreendedor Extintor') !== false) {
            $bgColorStyle = 'background-color: #ef4444;'; // Red
        } elseif (strpos($diagnosisResult['name'], 'Empreendedor Sobrecarregado') !== false) {
            $bgColorStyle = 'background-color: #3b82f6;'; // Blue
        }
        ?>
        <div style="border: 0.5pt solid #4b5563; margin-top: 0.4rem;">
            <div style=" font-weight: bold; color: white; text-align: center; {{ $bgColorStyle }}">
                <p style="margin: 0; padding: 0.5rem; font-size: 1em;"> {{ $diagnosisResult['name'] }}</p> </div>
            <div style="padding: 0.4rem; color: black; word-wrap: break-word;"> <p style="margin:0;">{{ $diagnosisResult['description'] }}</p> </div>
        </div>
    </section>

    {{-- ÚNICA seção de análise de pontos fortes/fracos --}}
    <section class="analysis page-break" style="margin-top: 2.5rem;">
        <div style="border: 1px solid;">
            <h3 style="text-align: center; font-size: 1em" >ANÁLISE DE POTENCIAIS PONTOS FORTES E OPORTUNIDADES DE DESENVOLVIMENTO</h3>
        </div>
        <table role="table">
            <thead>
            <tr>
                <th style="background-color: #22c55e; color: white; width: 50%; padding: 0.5rem; border: 1pt solid #4b5563;">PONTOS FORTES</th>
                <th style="background-color: #ef4444; color: white; width: 50%; padding: 0.25rem; border: 1pt solid #4b5563;">PONTOS PARA DESENVOLVER</th>
            </tr>
            </thead>
            <tbody>
            {{-- Um único loop, usando _source para diferenciar --}}
            @foreach ($allPoints as $point)
                @if (isset($point['strength_weakness_title']) && $point['strength_weakness_title'] !== '' && isset($point['strength_weakness']) && $point['strength_weakness'] !== '')
                    <tr style="border: 1pt solid #d1d5db;">
                        @if ($point['strength_weakness'] === 'strong')
                            <td style="padding: 0.5rem; border: 1pt solid #d1d5db;">
                                <strong style="font-weight: bold;">{{ $point['strength_weakness_title'] }}</strong>
                            </td>
                            <td style="padding: 0.5rem; border: 1pt solid #d1d5db;"></td> {{-- Célula vazia --}}
                        @else
                            <td style="padding: 0.5rem; border: 1pt solid #d1d5db;"></td> {{-- Célula vazia --}}
                            <td style="padding: 0.5rem; border: 1pt solid #d1d5db;">
                                <strong style="font-weight: bold;">{{ $point['strength_weakness_title'] }}</strong>
                            </td>
                        @endif
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </section>

    <section class="multiple-choice-answers page-break" style="width: 100%;">
        <div style="border: 1px solid; margin-top: 1em;">
            <h3 style="font-weight: bold; text-align: center; font-size: 1em">ANÁLISES GERAIS DA EMPRESA E EMPREENDEDOR</h3>
        </div>
        {{--Seção para apresentar o diagnostico das questões de múltipla escolha--}}
        @foreach ($allPoints as $point)
            @if(isset($point['_source']) && $point['_source'] === 'multiple_choice')
                <div style="margin-bottom: 1rem;">
                    <p style="font-weight: bold;">{{ $point['diagnosis_title'] }}</p>
                    <p>{{ $point['diagnosis'] }}</p>
                </div>
            @endif
        @endforeach
    </section>

    <section class="diagnosis-details page-break">
        <div style="border: 1px solid;">
            <h3 style="text-align: center; font-size: 1em; ">DETALHAMENTO DO DIAGNÓSTICO</h3>
        </div>
        {{-- Apenas para respostas SIMPLES com solução --}}
        @foreach($allPoints as $data)
            @if(isset($data['_source']) && $data['_source'] === 'simple' && isset($data['solution']) && $data['solution'] != '')
                <div style="margin-top: 1.5rem;">
                    <h2 style="font-size: 1rem; font-weight: 600;">{{ $data['diagnosis_title'] }}</h2>
                    <p style="margin-top: 0.5rem;"><span style="font-weight: bold;">Diagnóstico:</span></p>
                    <p style="word-wrap: break-word;">{{ $data['diagnosis'] }}</p>
                    <p style="margin-top: 0.5rem;"><span style="font-weight: bold;">Solução:</span></p>
                    <p style="word-wrap: break-word;">{{ $data['solution'] }}</p>
                </div>
            @endif
        @endforeach

    </section>
</main>

<footer class="container page-break">
    {{-- (Rodapé - sem alterações) --}}
    <div style="display:flex; flex-direction: column; items-center; justify-content: center; height: 100vh;" class="text-center">
        <p style="font-size: 1.125rem; font-weight: 600; color: #1f2937; text-align: center; margin-bottom: 20pt">
            Caso queira potencializar os resultados da sua empresa e alcançar seus objetivos de forma mais rápida e mais preparada,
            acesse nosso site:
        </p>
        <a href="https://bwolf.com.br" class="decoration-none">www.bwolf.com.br</a>
        <div style="margin-top: 1rem;" class="text-center">
            <p style="font-weight: bold;" class="text-center">Indique para seus amigos empresários que querem descobrir os pontos de melhoria e as oportunidades de crescimento do negócio!</p>
            <p><a href="https://form.bwolf.com.br">www.form.bwolf.com.br</a></p>
        </div>
        <p style="margin-top: 1rem; color: #374151;" class="text-center">Ficaremos sempre à sua disposição.</p>
        <p style="margin-top: 0.5rem; color: #111827; font-weight: bold;" class="text-center">BeWolf Consultoria Empresarial</p>
    </div>
</footer>

</body>
</html>
