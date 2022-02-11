<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
</head>
<body>
	<main id='app'>
        <div class="board">
            <columns-list 
                get-columns-path="{{ route('api.columns.index') }}" 
                save-new-column-path="{{ route('api.columns.store') }}"
                save-new-card-path="{{ route('api.cards.store') }}"
            ></columns-list>
        </div>
        <a class="export-db" target="_blank" href="{{ route('board.export.db'); }}">Export DB</a>
    </main>
    <footer class="footer">
        <div class="container">
            <p class="footer__copyright">© Copyright {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>