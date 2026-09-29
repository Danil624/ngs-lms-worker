<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мои курсы</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
            padding: 40px;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .progress {
            background: #ddd;
            height: 12px;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            background: #1677ff;
            height: 100%;
        }

        .btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 18px;
            background: #1677ff;
            color: white;
            text-decoration: none;
            border-radius: 7px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Мои курсы</h1>

    <p>Здравствуйте, {{ $user->name }}</p>

    @forelse($assignments as $assignment)

        <div class="card">

            <h2>{{ $assignment->course->title }}</h2>

            <p>{{ $assignment->course->description }}</p>

            <p>Прогресс: {{ $assignment->progress }}%</p>

            <div class="progress">
                <div
                    class="progress-bar"
                    style="width: {{ $assignment->progress }}%">
                </div>
            </div>

            <p>
                Дедлайн:
                {{ $assignment->deadline
                    ? \Carbon\Carbon::parse($assignment->deadline)->format('d.m.Y')
                    : 'не указан'
                }}
            </p>

            <a
                href="{{ route('my.courses.show', $assignment) }}"
                class="btn"
            >
                {{ $assignment->progress > 0
                    ? 'Продолжить курс'
                    : 'Начать курс'
                }}
            </a>

        </div>

    @empty

        <div class="card">
            Курсы пока не назначены.
        </div>

    @endforelse

</div>

</body>
</html>