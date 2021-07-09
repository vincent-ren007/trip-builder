<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Trip Builder</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <script src="/js/app.js"></script>

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
            .container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                align-content: center;
            }
        </style>
    </head>
    <body class="antialiased">
        <div class='container'>
            <h1>Trip Builder</h1>
            <p><a href='/docs'>API documentation</a></p>
        </div>
    </body>
</html>
