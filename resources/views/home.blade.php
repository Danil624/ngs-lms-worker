<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>НГ-Сервис — Обучение</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
        }

        .container {
            max-width: 900px;
            margin: 80px auto;
        }

        .card {
            background: white;
            padding: 40px;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,.08);
        }

        h1 {
            margin-bottom: 5px;
        }

        .buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 15px 25px;
            background: #1677ff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="card">

        <h1>НГ-Сервис</h1>

        <h2>Корпоративное обучение</h2>

        <p>Здравствуйте, Иван</p>

        <div class="buttons">
<a class="btn" href="{{ route('my.courses') }}">
    Мои курсы
</a>
<a class="btn" href="{{ route('hr.index') }}">
    HR-панель
</a>

        </div>

    </div>

</div>

</body>
</html>