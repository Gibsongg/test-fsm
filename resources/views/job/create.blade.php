@extends('layouts.main')

@section('content')
    <div class="row">
        <div class="col-8">
            <form action="{{route('jobs.store')}}" method="post">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Добавить заявку</label>
                    <input type="text" class="form-control" id="name" name="name">
                    @error('name')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Описание</label>
                    <textarea class="form-control" name="description" id="description"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Создать</button>
            </form>
        </div>
        <div class="col-4">

        </div>
    </div>
@endsection
