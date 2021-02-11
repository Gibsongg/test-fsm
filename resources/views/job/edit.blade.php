@extends('layouts.main')

@section('content')
    <div class="row">
        <div class="col-8">
            <form action="{{route('jobs.store')}}" method="post">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Заявка: {{$job->name}}</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{$job->name}}">
                    @error('name')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Описание</label>
                    <textarea type="email" class="form-control" id="description">{{$job->description}}</textarea>
                </div>
            </form>
        </div>
        <div class="col-4">
            <div class="mt-2">
                Дата: {{$job->created_at}}
            </div>
            <div class="mt-2">
                Статус:
                @foreach($status as $curStatus)
                <span class="badge badge-info">{{$curStatus}}</span>
                @endforeach
            </div>
            <div class="btn-group-vertical mt-2" role="group" aria-label="Basic example">
                @foreach($statuses as $key => $status)
                <a href="{{route('jobs.status', ['id' => $job->id, 'transition' => $key])}}" class="btn btn-light text-left">{{$status}}</a>
                @endforeach
            </div>

            <div style="margin-top: 20px">
                <a href="{{route('jobs.index')}}">В список работ</a>
            </div>
        </div>
    </div>

@endsection


