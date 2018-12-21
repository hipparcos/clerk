<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'clerk') }}</title>
    </head>
    <body>
        <h1>{{ config('app.name', 'clerk') }}</h1>
    </body>
</html>
