<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Negado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .error-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            max-width: 520px;
            width: 100%;
            text-align: center;
            padding: 3rem 2.5rem;
        }

        .error-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #fff3cd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .error-icon i {
            font-size: 2.2rem;
            color: #856404;
        }

        .error-code {
            font-size: 4rem;
            font-weight: 800;
            color: #1a1a2e;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .error-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }

        .error-message {
            color: #6c757d;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .btn-back {
            background: #3699FF;
            color: #fff;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .btn-back:hover {
            background: #187DE4;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(54, 153, 255, 0.35);
        }
    </style>
</head>

<body>
    <div class="error-card">
        <div class="error-icon">
            <i class="bi bi-shield-lock"></i>
        </div>
        <div class="error-code">403</div>
        <h1 class="error-title">Acesso Negado</h1>
        <p class="error-message">
            Você não tem permissão para acessar esta página.<br>
            Solicite ao administrador as permissões necessárias.
        </p>
        <a href="{{ url('/dashboard') }}" class="btn-back">
            <i class="bi bi-house-door"></i> Voltar ao Dashboard
        </a>
    </div>
</body>

</html>
