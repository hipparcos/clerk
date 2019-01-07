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
        <div id="app">
            <!-- navbar -->
            <clerk-nav
                app="{{ config('app.name', 'clerk') }}"
                v-on:flash="onFlash"
                ></clerk-nav>
            <!-- flash -->
            <div v-if="flash" class="container notification" :class="flashClass">
                <button class="delete" @click.prevent="flash = ''"></button>
                <p>@{{ flash }}</p>
            </div>
            <!-- top view -->
            <router-view class="container" name="top"
                v-on:flash="onFlash"
                ></router-view>
            <!-- main view -->
            <router-view class="container"
                v-on:flash="onFlash"
                ></router-view>
        </div>
        <script src="js/app.js"></script>
    </body>
</html>
