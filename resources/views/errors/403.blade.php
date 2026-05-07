<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg,rgb(7, 7, 7) 0%,rgb(24, 25, 26) 50%,rgb(35, 38, 41) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
        }

        .error-container {
            background: rgba(13, 13, 14, 0.9);
            border: 5px solid rgba(231, 227, 227, 0.1);
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 500px;
            margin: 2rem;
        }

        .code {
            font-size: 5rem;
            font-weight: 700;
            color: #ff4d4d;
            text-shadow: 0 0 20px rgba(255, 77, 77, 0.4);
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        h1 {
            color: #ffffff;
            margin-bottom: 1rem;
            font-size: 1.5rem;
            font-weight: 400;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
        }

        .message {
            color: #b0b0b0;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .back-link {
            display: inline-block;
            padding: 0.6rem 1.5rem;
            background: rgba(0, 212, 255, 0.1);
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 8px;
            color: #00d4ff;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.2s;
        }

        .back-link:hover {
            background: rgba(0, 212, 255, 0.2);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="code">403</div>
        <h1>Acceso Denegado</h1>
        <p class="message">No tienes permisos para acceder a esta sección.<br>Contacta al administrador si crees que es un error.</p>
        <a href="{{ url('/dashboard') }}" class="back-link">Volver al inicio</a>
    </div>
</body>
</html>
