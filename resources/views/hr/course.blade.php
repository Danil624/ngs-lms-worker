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