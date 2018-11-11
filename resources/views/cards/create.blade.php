@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create a new card</h2>
    {{ Form::open(array('action' => 'CardsController@store')) }}
        <div class="form-group">
            {{ Form::label('front', 'Front') }}
            {{ Form::text('front', '', ['class' => 'form-control', 'placeholder' => 'Front']) }}
        </div>
        <div class="form-group">
            {{ Form::label('back', 'Back') }}
            {{ Form::text('back', '', ['class' => 'form-control', 'placeholder' => 'Back']) }}
        </div>
        {{  Form::submit('Save this card', ['class' => 'btn btn-primary']) }}
    {{  Form::close() }}
</div>
@endsection