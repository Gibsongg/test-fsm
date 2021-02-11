@extends('layouts.main')

@section('content')
<h3>Заявки</h3>
<a href="{{route('claims.create')}}" class="btn btn-info">Добавить заявку</a>
<a href="{{route('diagram2')}}" target="_blank" class="btn btn-secondary">Показать рабочий процесс</a>
    <div style="margin-top: 20px">
        <table class="table">
            <thead>
                <tr>
                    <th>Задача</th>
                    <th>Статус</th>
                </tr>
            </thead>

            <tbody>
                @foreach($claims as $claim)
                <tr>
                    <td><a href="{{route('claims.edit', $claim->id)}}">{{$claim->name}}</a></td>
                    <td style="width: 100px"><span class="badge badge-primary p-1">{{$claim->status}}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
