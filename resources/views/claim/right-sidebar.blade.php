<div class="mb-3">
    <label for="status" class="form-label">Статус</label>
    <select class="form-control" id="status">
        <option value="">Выбрать исполнителя</option>
        @foreach($statuses as $key => $status)
            <option value="{{$key}}">{{$status}}</option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="status" class="form-label">Оценка</label>
    @if($model->estimate)
        {{$model->estimate}}
    @else
        <button data-open-estimate class="btn btn-info">Оценить</button>
    @endif;
</div>
