<!DOCTYPE html>
<html lang="en" class="has-navbar-fixed-top">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'clerk') }}</title>
        <link rel="stylesheet" href="css/app.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/solid.css" integrity="sha384-+0VIRx+yz1WBcCTXBkVQYIBVNEFH1eP6Zknm16roZCyeNg2maWEpk/l/KsyFKs7G" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/fontawesome.css" integrity="sha384-jLuaxTTBR42U2qJ/pm4JRouHkEDHkVqH0T1nyQXn1mZ7Snycpf6Rl25VBNthU4z0" crossorigin="anonymous">
    </head>
    <body>
        <nav class="navbar is-fixed-top" role="navigation" aria-label="main navigation">
            <div class="navbar-brand">
                <a class="navbar-item" href="/">
                    <i class="fas fa-building"></i>&nbsp;{{ config('app.name', 'clerk') }}
                </a>
            </div>

            <div id="navbar" class="navbar-menu">
                <div class="navbar-start">
                    <a class="navbar-item">
                        Home
                    </a>
                </div>

                <div class="navbar-end">
                    <div class="navbar-item">
                        <div class="buttons">
                            <a class="button is-primary">
                                <strong>Register</strong>
                            </a>
                            <a class="button is-light">
                                Log in
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container">
        </div>

        <script src="js/app.js"></script>
    </body>
</html>
