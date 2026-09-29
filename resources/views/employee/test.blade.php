<!DOCTYPE html>
<html lang="ru">

<head>

    <meta charset="UTF-8">

    <title>{{ $test->title }}</title>

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

        .question {
            padding: 20px 0;
            border-bottom: 1px solid #ddd;
        }

        .answer {
            margin: 10px 0;
        }

        button {
            padding: 12px 20px;
            background: #1677ff;
            color: white;
            border: 0;
            border-radius: 7px;
            cursor: pointer;
        }

    </style>

</head>

<body>

<div class="container">

    <a href="{{ route(
        'my.courses.show',
        $assignment
    ) }}">
        ← Назад к курсу
    </a>

    <div class="card">

        <h1>{{ $test->title }}</h1>

        <p>
            Проходной балл:
            {{ $test->passing_score }}%
        </p>

        <p>
            Попытка:
            {{ $attemptsCount + 1 }}
            из
            {{ $test->max_attempts }}
        </p>

    </div>


    <form
        method="POST"
        action="{{ route(
            'my.test.submit',
            $assignment
        ) }}"
    >

        @csrf

        <div class="card">

            @foreach($test->questions as $question)

                <div class="question">

                    <h3>
                        {{ $loop->iteration }}.
                        {{ $question->question }}
                    </h3>

                    @foreach($question->answers as $answer)

                        <div class="answer">

                            <label>

                                <input
                                    type="radio"
                                    name="answers[{{ $question->id }}]"
                                    value="{{ $answer->id }}"
                                    required
                                >

                                {{ $answer->answer }}

                            </label>

                        </div>

                    @endforeach

                </div>

            @endforeach

            <br>

            <button type="submit">
                Завершить тест
            </button>

        </div>

    </form>

</div>

</body>

</html>