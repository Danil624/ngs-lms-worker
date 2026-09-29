<!DOCTYPE html>
<html lang="ru">

<head>

    <meta charset="UTF-8">

    <title>Результаты обучения</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 40px;
            margin: 0;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
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

        .blue {
            color: #1677ff;
            font-weight: bold;
        }

    </style>

</head>

<body>

<div class="container">

    <a href="{{ route('hr.index') }}">
        ← HR-панель
    </a>

    <h1>Результаты обучения</h1>

    <div class="card">

        <table>

            <thead>

            <tr>

                <th>Сотрудник</th>

                <th>Курс</th>

                <th>Прогресс</th>

                <th>Статус</th>

                <th>Тест</th>

                <th>Балл</th>

                <th>Попытки</th>

                <th>Дедлайн</th>

            </tr>

            </thead>


            <tbody>

            @forelse($assignments as $assignment)

                <tr>

                    <td>
<a href="{{ route('hr.employee.show', $assignment->user) }}">
                            <strong>
                                {{ $assignment->user->name }} 
                            </strong>
                        </a>
                        
                        <br>

                        {{ $assignment->user->email }}

                    </td>


                    <td>

                        {{ $assignment->course->title }}

                    </td>


                    <td class="blue">

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

                        @if($assignment->last_test_attempt)

                            @if($assignment->last_test_attempt->passed)

                                <span class="green">
                                    ✓ Сдан
                                </span>

                            @else

                                <span class="red">
                                    ✗ Не сдан
                                </span>

                            @endif

                        @else

                            —

                        @endif

                    </td>


                    <td>

                        @if($assignment->last_test_attempt)

                            {{ $assignment->last_test_attempt->percent }}%

                        @else

                            —

                        @endif

                    </td>


                    <td>

                        {{ $assignment->test_attempts_count }}

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

                    <td colspan="8">
                        Назначений пока нет.
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>

</body>

</html>