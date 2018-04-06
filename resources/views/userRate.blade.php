@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Рейтинг пользователей TJournal по {{$title}}</div>

                <div class="card-body">
                    <table class="table table-light table-hover">
                        <thead>
                            <tr>
                                <th>Место</th>
                                <th>Имя</th>
                                <th>Рейтинг</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>#{{$loop->iteration}}</td>
                                <td>
                                    <a href="https://tjournal.ru/user/{{$user->tjId}}" target="_blank">
                                    {{$user->name}}
                                    </a>
                                </td>
                                @if($type == 'karma')
                                    <td>{{$user->karma}}</td>
                                    @else
                                    <td></td>
                                @endif
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
