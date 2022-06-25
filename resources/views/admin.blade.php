<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <title>Atbildes</title>
</head>
<body>
<x-title-menu/>
<a href="/admin/product"><h1 class="text-success">Preces</h1></a><br>
<a href="/admin/order"><h1 class="text-success">Pasūtījumi</h1></a><br>
<a href="/admin/category"><h1 class="text-success">Kategorijas</h1></a><br>
<a href="/admin/inquiry"><h1 class="text-success">Jautājumi</h1></a><br>
<a href="/admin/answer"><h1 class="text-success">Atbildes</h1></a><br>
<a href="/admin/review"><h1 class="text-success">Atsauces</h1></a><br>
<a href="/admin/user"><h1 class="text-success">Lietotāji</h1></a><br>
</body>
</html>