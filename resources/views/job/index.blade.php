@extends('layouts.main')

@section('content')
<h3>Заявки</h3>
<a href="{{route('jobs.create')}}" class="btn btn-info">Добавить работу</a>
<a href="{{route('diagram3')}}" target="_blank" class="btn btn-secondary">Показать рабочий процесс</a>
    <div style="margin-top: 20px">
        <table class="table">
            <thead>
                <tr>
                    <th>Работы</th>
                </tr>
            </thead>

            <tbody>
                @foreach($jobs as $job)
                <tr>
                    <td><a href="{{route('jobs.edit', $job->id)}}">{{$job->name}}</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
