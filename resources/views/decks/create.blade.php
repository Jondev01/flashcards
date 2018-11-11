@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create A Deck</h2>
    {{ Form::open(array('action' => 'DecksController@store')) }}
        <div class="form-group">
            {{ Form::label('name', 'Name') }}
            {{ Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Name']) }}
        </div>
        <div class="form-group">
            {{ Form::label('description', 'Add a description') }}
            {{ Form::text('description', '', ['class' => 'form-control', 'placeholder' => 'description']) }}
        </div>
        {{  Form::submit('Create Deck', ['class' => 'btn btn-primary']) }}
    {{  Form::close() }}
</div>
@endsection