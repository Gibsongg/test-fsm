<?php
use App\Dictionary\TaskStatusDictionary;
?>
@extends('layouts.main')

@section('content')
    <div class="row">
        <div class="col-8">
            <form action="{{route('claims.store')}}" method="post">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Заявка: {{$claim->name}}</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{$claim->name}}">
                    @error('name')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Описание</label>
                    <textarea type="email" class="form-control" id="description">{{$claim->description}}</textarea>
                </div>
            </form>
        </div>
        <div class="col-4">
            <div class="mt-2">
                Дата: <span class="badge badge-info">{{$claim->created_at}}</span>
            </div>
            <div class="mt-2">
                Статус: <span class="badge badge-info">{{$status}}</span>
            </div>
            <div class="btn-group-vertical mt-2" role="group" aria-label="Basic example">
                @foreach($statuses as $key => $status)
                <a href="{{route('claims.status', ['id' => $claim->id, 'transition' => $key])}}" class="btn btn-light text-left">{{$status}}</a>
                @endforeach
            </div>

            <div class="mt-2">
                <a href="{{route('claims.index')}}">В список заявок</a>
            </div>
        </div>

    </div>




@endsection


