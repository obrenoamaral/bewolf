<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Diagnóstico</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 18px;
            font-weight: bold;
            margin-top: 15px;
        }
        p {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .diagnostic-container {
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .diagnostic-label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Relatório de Diagnóstico</h1>

    @foreach($reportData as $data)
        <div class="diagnostic-container">
            <h2>{{ $data['question'] }}</h2>

            <span class="diagnostic-label">Diagnóstico:</span>
            <p>{{ $data['diagnosis'] }}</p>

            <span class="diagnostic-label">Solução:</span>
            <p>{{ $data['solution'] }}</p>
        </div>
    @endforeach
</div>
</body>
</html>
