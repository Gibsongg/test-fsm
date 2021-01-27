@extends('layouts.main')

@section('content')
<a href="{{route('tasks.create')}}" class="btn btn-info">Добавить задачу</a>
<a href="{{route('diagram')}}" target="_blank" class="btn btn-secondary">Показать рабочий процесс</a>
    <div style="margin-top: 20px">
        <table class="table">
            <thead>
                <tr>
                    <th>Задача</th>
                </tr>
            </thead>

            <tbody>
                @foreach($tasks as $task)
                <tr>
                    <td><a href="{{route('tasks.edit', $task->id)}}">{{$task->name}}</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
