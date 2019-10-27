<!doctype html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.6/css/jquery.dataTables.min.css"/>

<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.6/js/jquery.dataTables.min.js"></script>

<div class="container">
    @if (Session::has('message'))
        <div class="flash alert">
            <p>{{ Session::get('message') }}</p>
        </div>
    @endif
    @yield('content')
</div>
</body>
</html>
