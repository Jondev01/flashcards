@extends('layouts/app')

@section('content')
    @if( count($decks) > 0 )
        @foreach($decks as $deck)
        <h1>{{$deck->name}}</h1>
        <p>{{$deck->description}}</p>
        @endforeach
    @endif
@endsection