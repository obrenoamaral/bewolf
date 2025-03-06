<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Diagnóstico Empresarial</title>
    <style>
        /* Estilos globais */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* Container principal */
        .container {
            width: 100%;
            padding: 16px;
            display: flex;
            justify-content: center;
        }

        /* Cartão central */
        .content {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 650px;
            width: 100%;
            margin-top: 40px;
        }

        /* Logo */
        .logo {
            width: 200px;
            margin: 0 auto 30px;
            display: block;
        }

        /* Títulos e parágrafos */
        .text-lg {
            font-size: 1.25rem;
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .text-base {
            font-size: 1rem;
            color: #4a5568;
            line-height: 1.5;
            margin-bottom: 24px;
        }

        .font-bold {
            font-weight: bold;
        }

        /* Botão */
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #000000;
            color: #fff;
            font-weight: bold;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0f0f0f;
        }

        /* Rodapé */
        .footer {
            text-align: center;
            font-size: 0.875rem;
            color: #718096;
            margin-top: 40px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="content">
        <img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('logo-preto.png')))}}" alt="Logotipo" class="logo">

        <p>{{ $emailContent->greeting }}</p>

        <p>
            {{ $emailContent->intro_text }}
        </p>

        <p>
            {{ $emailContent->closing_text }}
        </p>

        @if ($emailContent && $emailContent->button_link && $emailContent->button_text)
            <a href="{{ $emailContent->button_link }}" class="btn">{{ $emailContent->button_text }}</a>
        @endif

        <p class="text-base footer">
            Atenciosamente,<br>
            <span class="font-bold">Equipe BeWolf</span>
        </p>
    </div>
</div>
</body>
</html>
