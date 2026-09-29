<!DOCTYPE html>
<html lang="ru">

<head>

    <meta charset="UTF-8">

    <title>
        {{ $lesson->title }}
    </title>

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
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .btn {
            padding: 12px 20px;
            background: #1677ff;
            color: white;
            border: 0;
            border-radius: 7px;
            cursor: pointer;
        }

        .completed {
            color: green;
            font-weight: bold;
        }

        .material {
            margin: 25px 0;
            line-height: 1.6;
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

        <h1>
            {{ $lesson->title }}
        </h1>

        <p>
            Тип материала:
            {{ $lesson->type }}
        </p>


        <div class="material">

            @if($lesson->type === 'text')

                {!! nl2br(
                    e($lesson->content)
                ) !!}


            @elseif($lesson->type === 'video')

                <p>
                    Видео:
                </p>

                <a
                    href="{{ $lesson->content }}"
                    target="_blank"
                >
                    Открыть видео
                </a>


            @elseif($lesson->type === 'pdf')

                <p>
                    PDF-документ:
                </p>

                <a
                    href="{{ $lesson->content }}"
                    target="_blank"
                >
                    Открыть PDF
                </a>


            @elseif($lesson->type === 'audio')

                <p>
                    Аудиоматериал:
                </p>

                <a
                    href="{{ $lesson->content }}"
                    target="_blank"
                >
                    Открыть аудио
                </a>


            @elseif($lesson->type === 'file')

                <a
                    href="{{ $lesson->content }}"
                    target="_blank"
                >
                    Скачать файл
                </a>


            @else

                {{ $lesson->content }}

            @endif

        </div>


        @if($progress->status === 'completed')

            <p class="completed">

                ✅ Материал изучен

            </p>

        @else

            <form
                method="POST"
                action="{{ route(
                    'my.lessons.complete',
                    [
                        $assignment,
                        $lesson
                    ]
                ) }}"
            >

                @csrf

                <button
                    class="btn"
                    type="submit"
                >

                    ✓ Материал изучен

                </button>

            </form>

        @endif

    </div>

</div>

</body>

</html>