<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sitio en Construcción - {{ config('app.name') }}</title>
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
        
        .maintenance-container {
            background: rgba(13, 13, 14, 0.9);
            border: 5px solid rgba(231, 227, 227, 0.1);
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 500px;
            margin: 2rem;
        }
        
        .icon {
            font-size: 1rem;
            margin-bottom: 1rem;
            color: #00d4ff;
            text-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
        }
        
        h1 {
            color: #ffffff;
            margin-bottom: 1rem;
            font-size: 2.5rem;
            font-weight: 300;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 1rem;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #00d4ff, #0099cc);
            width: 75%;
            animation: progress 2s ease-in-out infinite;
            box-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        }
        
        @keyframes progress {
            0% { width: 0%; }
            50% { width: 75%; }
            100% { width: 75%; }
        }
        
        .status {
            color: #b0b0b0;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="icon">🌟</div>
        <h1>Sitio en Construcción</h1>
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
        <div class="status">Trabajando en el desarrollo...</div>
    </div>
</body>
</html>
