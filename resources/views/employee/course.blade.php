<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{{ $assignment->course->title }}</title>

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

        .lesson {
            padding: 18px 0;
            border-bottom: 1px solid #ddd;
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            background: #1677ff;
            color: white;
            text-decoration: none;
            border-radius: 7px;
            margin-top: 10px;
        }

        .completed {
            color: green;
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
    </style>
</head>

<body>

<div class="container">

    <a href="{{ route('my.courses') }}">
        ← Мои курсы
    </a>

    <h1>{{ $assignment->course->title }}</h1>

    <p>{{ $assignment->course->description }}</p>
    
@if(session('test_result'))

    @php
        $result = session('test_result');
    @endphp

    <div class="card">

        <h2>Результат теста</h2>

        <p>
            Правильных ответов:
            {{ $result['correct'] }}
            из
            {{ $result['total'] }}
        </p>

        <p>
            Результат:
            {{ $result['percent'] }}%
        </p>

        <p>
            Попытка:
            {{ $result['attempt'] }}
        </p>

        @if($result['passed'])

            <strong class="completed">
                ✅ ТЕСТ СДАН
            </strong>

        @else

            <strong style="color:red;">
                ❌ ТЕСТ НЕ СДАН
            </strong>

        @endif

    </div>

@endif
    <div class="card">

        <h2>Прогресс</h2>

        <p>{{ $assignment->progress }}%</p>

        <div class="progress">
            <div
                class="progress-bar"
                style="width: {{ $assignment->progress }}%">
            </div>
        </div>

    </div>

    <div class="card">

        <h2>Уроки</h2>

        @forelse($assignment->course->lessons as $lesson)

            @php
                $progress = $lessonProgress[$lesson->id] ?? null;
            @endphp

            <div class="lesson">

                <strong>
                    {{ $loop->iteration }}. {{ $lesson->title }}
                </strong>

                <br>

                @if($progress && $progress->status === 'completed')

                    <span class="completed">
                        ✅ Пройден
                    </span>

                @elseif($progress)

                    🟡 В процессе

                @else

                    ⚪ Не начат

                @endif

                <br>

                <a
                    class="btn"
                    href="{{ route('my.lessons.show', [$assignment, $lesson]) }}"
                >

                    @if($progress && $progress->status === 'completed')
                        Посмотреть
                    @elseif($progress)
                        Продолжить
                    @else
                        Начать
                    @endif

                </a>

            </div>

        @empty

            <p>Уроков пока нет.</p>

        @endforelse

    </div>
@if($assignment->course->test)

    <div class="card">

        <h2>Итоговый тест</h2>

        <p>
            {{ $assignment->course->test->title }}
        </p>

        <p>
            Вопросов:
            {{ $assignment->course->test->questions->count() }}
        </p>

        <p>
            Проходной балл:
            {{ $assignment->course->test->passing_score }}%
        </p>

        <p>
            Попыток:
            {{ $assignment->course->test->max_attempts }}
        </p>

        @if($assignment->status === 'completed')

            <span class="completed">
                ✅ Тест сдан
            </span>

        @else

            <a
                href="{{ route('my.test.show', $assignment) }}"
                class="btn"
            >
                Пройти тест
            </a>

        @endif

    </div>

@endif
</div>

</body>
</html>