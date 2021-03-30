<html>
<head>
    <title>Задачи</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</head>
<body>
<h1 class="m-2">Workflow</h1>
<hr/>

<div class="container">
    <ul class="nav bg-light mb-5">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="{{route('tasks.index')}}">Задачи</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('claims.index')}}">Заявки</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('jobs.index')}}">Работы</a>
        </li>

        <li class="nav-item float-lg-right">
            <a class="nav-link" href="http://localhost:8081/list.html">Графы</a>
        </li>
        <li class="nav-item float-lg-right">
            <a class="nav-link" href="http://localhost:8081/index.html">Конструктор</a>
        </li>

    </ul>
    @yield('content')
</div>
</body>
</html>
