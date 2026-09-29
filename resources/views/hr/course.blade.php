<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>{{ $course->title }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 40px;
            margin: 0;
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

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        input[type="radio"] {
            width: auto;
            margin-right: 10px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        button {
            padding: 12px 20px;
            background: #1677ff;
            color: white;
            border: 0;
            border-radius: 7px;
            cursor: pointer;
        }

        .answer-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .answer-row input[type="text"] {
            margin: 0;
        }

        .lesson,
        .question,
        .assignment {
            padding: 15px 0;
            border-bottom: 1px solid #ddd;
        }

        .success {
            background: #e8f7e8;
            color: green;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .muted {
            color: #666;
        }
    </style>
</head>

<body>

<div class="container">

    <a href="{{ route('hr.index') }}">← Назад</a>

    <h1>{{ $course->title }}</h1>

    <p>{{ $course->description }}</p>


    @if(session('success'))

        <div class="success">
            ✅ {{ session('success') }}
        </div>

    @endif


    <!-- ДОБАВЛЕНИЕ УРОКА -->

    <div class="card">

        <h2>Добавить урок</h2>

        <form method="POST"
              action="{{ route('hr.lessons.store', $course) }}">

            @csrf

            <label>Название урока</label>

            <input
                type="text"
                name="title"
                placeholder="Название урока"
                required
            >


            <label>Тип материала</label>

            <select name="type">

                <option value="text">
                    Текст
                </option>

                <option value="pdf">
                    PDF
                </option>

                <option value="video">
                    Видео
                </option>

                <option value="audio">
                    Аудио
                </option>

                <option value="file">
                    Файл
                </option>

            </select>


            <label>Материал</label>

            <textarea
                name="content"
                placeholder="Текст урока или ссылка на материал"
            ></textarea>


            <button type="submit">
                Добавить урок
            </button>

        </form>

    </div>


    <!-- НАЗНАЧЕНИЕ КУРСА -->

    <div class="card">

        <h2>Назначить курс сотруднику</h2>

        <form
            method="POST"
            action="{{ route('hr.assignments.store', $course) }}"
        >

            @csrf

            <label>Сотрудник</label>

            <select name="user_id" required>

                <option value="">
                    Выберите сотрудника
                </option>

                @foreach($users as $user)

                    <option value="{{ $user->id }}">

                        {{ $user->name }}
                        —
                        {{ $user->email }}

                    </option>

                @endforeach

            </select>


            <label>Дедлайн</label>

            <input
                type="date"
                name="deadline"
            >


            <button type="submit">
                Назначить курс
            </button>

        </form>


        <hr>


        <h3>Курс назначен сотрудникам</h3>


        @forelse($course->assignments as $assignment)

            <div class="assignment">

                <strong>
                    {{ $assignment->user->name }}
                </strong>

                <br>

                <span class="muted">
                    {{ $assignment->user->email }}
                </span>

                <br><br>

                Прогресс:
                <strong>
                    {{ $assignment->progress }}%
                </strong>

                <br>

                Статус:
                <strong>
                    {{ $assignment->status }}
                </strong>

                <br>

                Дедлайн:

                <strong>

                    {{ $assignment->deadline
                        ? \Carbon\Carbon::parse(
                            $assignment->deadline
                        )->format('d.m.Y')
                        : 'не указан'
                    }}

                </strong>

            </div>

        @empty

            <p>
                Курс пока никому не назначен.
            </p>

        @endforelse

    </div>


    <!-- ИТОГОВЫЙ ТЕСТ -->

    <div class="card">

        <h2>Итоговый тест</h2>


        @if($course->test)

            <p>

                <strong>
                    {{ $course->test->title }}
                </strong>

            </p>

            <p>
                Проходной балл:
                {{ $course->test->passing_score }}%
            </p>

            <p>
                Попыток:
                {{ $course->test->max_attempts }}
            </p>


            <hr>


            <h3>Добавить вопрос</h3>


            <form
                method="POST"
                action="{{ route(
                    'hr.questions.store',
                    $course->test
                ) }}"
            >

                @csrf


                <label>Вопрос</label>

                <input
                    type="text"
                    name="question"
                    required
                >


                <label>
                    Варианты ответов
                </label>


                <div id="answers">

                    <div class="answer-row">

                        <input
                            type="radio"
                            name="correct_answer"
                            value="0"
                            required
                        >

                        <input
                            type="text"
                            name="answers[]"
                            placeholder="Вариант ответа"
                            required
                        >

                    </div>


                    <div class="answer-row">

                        <input
                            type="radio"
                            name="correct_answer"
                            value="1"
                            required
                        >

                        <input
                            type="text"
                            name="answers[]"
                            placeholder="Вариант ответа"
                            required
                        >

                    </div>

                </div>


                <button
                    type="button"
                    onclick="addAnswer()"
                >
                    + Добавить вариант
                </button>


                <br><br>


                <button type="submit">
                    Добавить вопрос
                </button>

            </form>


            <hr>


            <h3>Вопросы теста</h3>


            @forelse(
                $course->test->questions
                as $question
            )

                <div class="question">

                    <strong>

                        {{ $loop->iteration }}.

                        {{ $question->question }}

                    </strong>


                    <br><br>


                    @foreach(
                        $question->answers
                        as $answer
                    )

                        <div>

                            {{ $answer->is_correct
                                ? '✅'
                                : '○'
                            }}

                            {{ $answer->answer }}

                        </div>

                    @endforeach

                </div>


            @empty

                <p>
                    Вопросов пока нет.
                </p>

            @endforelse


        @else


            <form
                method="POST"
                action="{{ route(
                    'hr.tests.store',
                    $course
                ) }}"
            >

                @csrf


                <label>
                    Название теста
                </label>

                <input
                    type="text"
                    name="title"
                    value="Итоговый тест"
                    required
                >


                <label>
                    Проходной балл, %
                </label>

                <input
                    type="number"
                    name="passing_score"
                    value="80"
                    min="0"
                    max="100"
                    required
                >


                <label>
                    Количество попыток
                </label>

                <input
                    type="number"
                    name="max_attempts"
                    value="3"
                    min="1"
                    required
                >


                <button type="submit">
                    Создать тест
                </button>

            </form>


        @endif

    </div>


    <!-- СПИСОК УРОКОВ -->

    <div class="card">

        <h2>Уроки</h2>


        @forelse(
            $course->lessons
            as $lesson
        )

            <div class="lesson">

                <strong>
                    {{ $loop->iteration }}.
                    {{ $lesson->title }}
                </strong>

                <br>

                Тип:
                {{ $lesson->type }}

            </div>


        @empty

            <p>
                Уроков пока нет.
            </p>

        @endforelse

    </div>

</div>


<script>

function addAnswer() {

    const container =
        document.getElementById('answers');

    const index =
        container.children.length;

    const row =
        document.createElement('div');

    row.className = 'answer-row';

    row.innerHTML = `

        <input
            type="radio"
            name="correct_answer"
            value="${index}"
            required
        >

        <input
            type="text"
            name="answers[]"
            placeholder="Вариант ответа"
            required
        >

        <button
            type="button"
            onclick="removeAnswer(this)"
        >
            Удалить
        </button>

    `;

    container.appendChild(row);

}


function removeAnswer(button) {

    button.parentElement.remove();

    const rows =
        document.querySelectorAll(
            '#answers .answer-row'
        );

    rows.forEach((row, index) => {

        const radio =
            row.querySelector(
                'input[type="radio"]'
            );

        radio.value = index;

    });

}

</script>

</body>
</html>