<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>HR-панель</title>

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

        input, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        button {
            padding: 12px 20px;
            background: #1677ff;
            color: white;
            border: 0;
            border-radius: 7px;
            cursor: pointer;
        }

        .course {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>HR-панель</h1>
<a href="{{ route('hr.results') }}"
   style="
       display:inline-block;
       padding:12px 20px;
       background:#1677ff;
       color:white;
       text-decoration:none;
       border-radius:7px;
       margin-bottom:20px;
   ">
    Результаты обучения
</a>
    <div class="card">

        <h2>Создать курс</h2>

        <form method="POST" action="{{ route('hr.courses.store') }}">
            @csrf

            <input
                type="text"
                name="title"
                placeholder="Название курса"
                required
            >

            <textarea
                name="description"
                placeholder="Описание курса"
            ></textarea>

            <button type="submit">
                Создать курс
            </button>

        </form>

    </div>

    <div class="card">

        <h2>Курсы</h2>

        @forelse($courses as $course)

           <div class="course">
    <a href="{{ route('hr.courses.show', $course) }}">
        <strong>{{ $course->title }}</strong>
    </a>

    <br>

    {{ $course->description }}
</div>

        @empty

            <p>Курсов пока нет.</p>

        @endforelse

    </div>

</div>

</body>
</html>