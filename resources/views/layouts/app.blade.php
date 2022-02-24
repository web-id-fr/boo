<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>

    <!-- Fonts -->
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="flex-center position-ref full-height">
        <div class="top-right links">
            <a href="{{ url('/') }}">Home</a>
        </div>
        @yield('content')
    </div>

</body>
</html>
