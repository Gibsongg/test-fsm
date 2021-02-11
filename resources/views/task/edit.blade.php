<?php
use App\Dictionary\TaskStatusDictionary;
?>
@extends('layouts.main')

@section('content')

    <div class="row">
        <div class="col-8">
            <form action="{{route('tasks.store')}}" method="post">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Задача</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{$task->name}}">
                    @error('name')
                    <div class="alert alert-danger">{{$message}}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Описание</label>
                    <textarea type="email" class="form-control" id="description">{{$task->description}}</textarea>
                </div>
            </form>


        </div>
        <div class="col-4">
            <div class="mt-2">
                Дата: <span>{{$task->created_at}}</span>
            </div>
            <div class="mb-2">
                Статус: <span class="badge badge-info">{{$status}}</span>
            </div>
            <div class="btn-group-vertical" role="group" aria-label="Basic example">
                @foreach($statuses as $key => $status)
                <a href="{{route('tasks.status', ['id' => $task->id, 'transition' => $key])}}" class="btn btn-light text-left">{{$status}}</a>
                @endforeach
            </div>

            <div style="margin-top: 20px">
                <a href="{{route('tasks.index')}}">В список задач</a>
            </div>
        </div>
    </div>




@endsection


