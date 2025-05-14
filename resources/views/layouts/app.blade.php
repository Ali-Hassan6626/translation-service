<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Translation Manager</title>

    {{-- Bootstrap CSS (optional, for styling) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Custom Styles (optional) --}}
    <style>
        body {
            background-color: #f8f9fa;
        }
    </style>

    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">Translation Manager</a>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>

    {{-- Bootstrap JS (optional for dropdowns etc.) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>