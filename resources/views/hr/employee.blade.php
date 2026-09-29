<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">

    <title>{{ $user->name }} — обучение</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #f5f5f5;
        }

        .green {
            color: green;
            font-weight: bold;
        }

        .red {
            color: red;
            font-weight: bold;
        }

        .log {
            padding: 12px 0;
            border-bottom: 1px solid #ddd;
        }

        .date {
            color: #777;
            font-size: 14px;
        }
    </style>
</head>

<body>

<div class="container">

    <a href="{{ route('hr.results') }}">
        ← Результаты обучения
    </a>


    <h1>{{ $user->name }}</h1>

    <p>{{ $user->email }}</p>


    <!-- КУРСЫ -->

    <div class="card">

        <h2>Назначенные курсы</h2>

        <table>

            <thead>
            <tr>
                <th>Курс</th>
                <th>Прогресс</th>
                <th>Статус</th>
                <th>Дедлайн</th>
            </tr>
            </thead>

            <tbody>

            @forelse($assignments as $assignment)

                <tr>

                    <td>
                        {{ $assignment->course->title }}
                    </td>

                    <td>
                        {{ $assignment->progress }}%
                    </td>

                    <td>

                        @if($assignment->status === 'completed')

                            <span class="green">
                                Завершён
                            </span>

                        @elseif($assignment->status === 'in_progress')

                            В процессе

                        @elseif($assignment->status === 'overdue')

                            <span class="red">
                                Просрочен
                            </span>

                        @else

                            Не начат

                        @endif

                    </td>

                    <td>

                        {{ $assignment->deadline
                            ? \Carbon\Carbon::parse(
                                $assignment->deadline
                            )->format('d.m.Y')
                            : '—'
                        }}

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="4">
                        Курсы не назначены.
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>


    <!-- ТЕСТЫ -->

    <div class="card">

        <h2>Попытки тестирования</h2>

        <table>

            <thead>

            <tr>
                <th>Курс</th>
                <th>Попытка</th>
                <th>Балл</th>
                <th>Результат</th>
                <th>Дата</th>
            </tr>

            </thead>


            <tbody>

            @forelse($attempts as $attempt)

                <tr>

                    <td>
                        {{ $attempt->test->course->title ?? '—' }}
                    </td>

                    <td>
                        {{ $attempt->attempt_number }}
                    </td>

                    <td>
                        {{ $attempt->percent }}%
                    </td>

                    <td>

                        @if($attempt->passed)

                            <span class="green">
                                ✓ Сдан
                            </span>

                        @else

                            <span class="red">
                                ✗ Не сдан
                            </span>

                        @endif

                    </td>

                    <td>
                        {{ $attempt->created_at->format('d.m.Y H:i') }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="5">
                        Тесты ещё не проходились.
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>


    <!-- ЖУРНАЛ -->

    <div class="card">

        <h2>Журнал действий</h2>


        @forelse($logs as $log)

            <div class="log">

                <strong>

                    @switch($log->event)

                        @case('COURSE_OPENED')
                            Открыл курс
                            @break

                        @case('LESSON_OPENED')
                            Открыл урок
                            @break

                        @case('LESSON_COMPLETED')
                            Завершил урок
                            @break

                        @case('TEST_STARTED')
                            Начал тест
                            @break

                        @case('TEST_FINISHED')
                            Завершил тест
                            @break

                        @case('COURSE_COMPLETED')
                            Завершил курс
                            @break

                        @default
                            {{ $log->event }}

                    @endswitch

                </strong>


                @if($log->course)

                    <br>

                    Курс:
                    {{ $log->course->title }}

                @endif


                @if($log->lesson)

                    <br>

                    Урок:
                    {{ $log->lesson->title }}

                @endif


                @if(
                    $log->event === 'TEST_FINISHED'
                    && $log->data
                )

                    <br>

                    Результат:
                    {{ $log->data['percent'] ?? 0 }}%

                    —

                    {{ ($log->data['passed'] ?? false)
                        ? 'сдан'
                        : 'не сдан'
                    }}

                @endif


                <div class="date">

                    {{ $log->created_at->format(
                        'd.m.Y H:i:s'
                    ) }}

                </div>

            </div>


        @empty

            <p>
                Действий пока нет.
            </p>

        @endforelse

    </div>

</div>

</body>

</html>