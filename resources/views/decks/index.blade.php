@extends('layouts/app')

@section('content')
    <div class="container">
        <select id="selectDeck">
            @if(count($decks) > 0)
                @foreach($decks as $deck)
                    <option id="{{$deck->id}}">{{$deck->name}}</option>
                @endforeach
            @endif
        </select>
        <div class="card" id="card">
            Current card
        </div>
    </div>
    <script>
        let decks = {!!$decks!!};
        flipCard(1);

        function getCurrentDeck(){
            let e = document.getElementById('selectDeck');
            let deckId = e.options[e.selectedIndex].id
            let curDeck;
            let cards;
            for(let key in decks){
                if(decks[key].id == deckId){
                    curDeck = decks[key];
                }
            }
            return curDeck;
        }

        function getCards(){

        }

        function flipCard(id){
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                document.getElementById("card").innerHTML = this.responseText;
                }
            };
            console.log('{!! route('decks.index')!!}'+`/${id}`);
            xhttp.open('GET', '{!! route('decks.index')!!}'+`/${id}`, true);
            xhttp.send();
        }
    </script>
@endsection
