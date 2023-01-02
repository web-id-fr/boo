<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ __('Boo!') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap"
        rel="stylesheet">
    <style>
    body {
        display: grid;
        place-items: center;
        background: #EBEDEA;
        font-family: 'Fredoka One', cursive;
    }

    h1 {
        font-size: 5rem;
    }

    img {
        max-width: 512px;
        max-height: 512px;
    }
    </style>
</head>
<body>
<h1>{{ __('Boo!') }}</h1>
<img src="{{ asset('images/boo.png') }}" alt="" />
</body>
</html>
