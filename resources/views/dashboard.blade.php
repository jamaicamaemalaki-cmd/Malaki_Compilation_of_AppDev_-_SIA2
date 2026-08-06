<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Breakup Songs</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ffeef8 0%, #ffb3d9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(255, 105, 180, 0.2);
            padding: 60px 40px;
            max-width: 600px;
            text-align: center;
        }

        h1 {
            color: #ff1493;
            font-size: 42px;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .subtitle {
            color: #ff69b4;
            font-size: 18px;
            margin-bottom: 50px;
            font-weight: 300;
        }

        .button-group {
            display: flex;
            gap: 20px;
            flex-direction: column;
        }

        .btn {
            display: inline-block;
            padding: 18px 40px;
            font-size: 18px;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff1493, #ff69b4);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(255, 20, 147, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #ffb3d9, #ffc9e3);
            color: #c71585;
        }

        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(255, 105, 180, 0.3);
        }

        .icon {
            font-size: 20px;
            margin-right: 10px;
        }

        @media (max-width: 600px) {
            .container {
                padding: 40px 25px;
            }

            h1 {
                font-size: 32px;
            }

            .btn {
                padding: 15px 30px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>💔 Breakup Songs Hub</h1>
        <p class="subtitle">Your go-to playlist for heartbreak moments</p>
        
        <div class="button-group">
            <a href="{{ route('items.index') }}" class="btn btn-primary">
                <span class="icon">🎵</span>Browse Items
            </a>
            <a href="{{ route('songs.index') }}" class="btn btn-primary">
                <span class="icon">📋</span>Song List
            </a>
            <a href="{{ route('songs.create') }}" class="btn btn-secondary">
                <span class="icon">➕</span>Add New Song
            </a>
        </div>
    </div>
</body>
</html>
