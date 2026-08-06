<!DOCTYPE html>
<html>
<head>
    <title>Breakup Songs Recommendation</title>
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
            color: #333;
        }

        header {
            background: linear-gradient(135deg, #ff1493, #ff69b4);
            color: white;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(255, 20, 147, 0.3);
        }

        header h1 {
            font-size: 38px;
            font-weight: 700;
            margin: 0;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        h2 {
            color: #c71585;
            font-size: 28px;
            margin-bottom: 25px;
            font-weight: 700;
        }

        a {
            text-decoration: none;
            color: #ff1493;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #c71585;
        }

        footer {
            margin-top: 50px;
            text-align: center;
            color: #c71585;
            font-weight: 600;
            padding: 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #ff1493, #ff69b4);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(255, 20, 147, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 20, 147, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #ffb3d9, #ffc9e3);
            color: #c71585;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #ff99cc, #ffb3d9);
        }
    </style>
</head>
<body>
    <header>
        <h1>🎧 Breakup Songs Hub</h1>
    </header>

    <div class="container">
        @yield('content')
    </div>

    <footer>© 2026 Breakup System</footer>
</body>
</html>