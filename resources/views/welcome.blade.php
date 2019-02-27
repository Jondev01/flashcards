@extends('layouts.app')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="card-text">
                <h3>Create your own decks of flashcards today and start practicing.</h2>
                <p><a href="{{route('register')}}">Join now!</a></p>
            </div>
        </div>
    </div>
</div>
@endsection