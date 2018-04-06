@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Рейтинг мемов TJournal по {{$rateType}}</div>

                <div class="card-body">
                    <table class="table table-light table-hover">
                        <thead>
                            <tr>
                                <th>Место</th>
                                <th>Название</th>
                                <th>Рейтинг</th>
                                <th>Кол-во комментариев</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($mems as $mem)
                            <tr>
                                <td>#{{$loop->iteration}}</td>
                                <td>
                                    <a href="https://tjournal.ru/{{$mem->entryId}}" target="_blank">
                                    {{$mem->name}}
                                    </a>
                                </td>
                                <td>{{$mem->likes}}</td>
                                <td>{{$mem->commentCount}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
