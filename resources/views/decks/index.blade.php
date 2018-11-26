@extends('layouts/app')

@section('content')
    <div class="container">
        <select id="selectDeck" onchange="updateDeck()">
            @if(count($decks) > 0)
                @foreach($decks as $deck)
                    <option id="{{$deck->id}}" value="{{$deck->id}}">{{$deck->name}}</option>
                @endforeach
            @endif
        </select>
        <button onclick="nextCard()">Next</button>
        <div class="card noselect" id="card" onclick="flipCard()">
            Current card
        </div>
    </div>

    <!------- javascript --------->
    <script type="application/javascript">
        let decks = {!!$decks!!};
        let deck, cards, card;
        let front = true;
        updateDeck();
        
        function updateDeck(){
            deck = getCurrentDeck();
            updateCards();
        }
        function getCurrentDeck(){
            let e = document.getElementById('selectDeck');
            let deckId = e.options[e.selectedIndex].id
            let curDeck;
            for(let key in decks){
                if(decks[key].id == deckId){
                    curDeck = decks[key];
                }
            }
            return curDeck;
        }

        function updateCards(){
            let id = deck.id;
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                //on success
                if (this.readyState == 4 && this.status == 200) {
                cards = JSON.parse(this.responseText);
                nextCard();
                }
            };
            //gets the url via hack
            xhttp.open('GET', '{!! route('decks.index')!!}'+`/${id}`, true);
            xhttp.send();
        }

        function nextCard(){
            //get random card
            let temp = cards[Math.floor(Math.random()*cards.length)]; 
            //make sure a new card was chosen, otherwise try again
            if(card && card.id === temp.id && cards.length>1)
                nextCard();
            else
                card = temp;
            front = true;
            document.getElementById("card").innerHTML = card.front;
        }

        function flipCard(){
            if(front)
                document.getElementById("card").innerHTML = card.back;
            else 
                document.getElementById("card").innerHTML = card.front;
            front = !front;
        }

    </script>
@endsection
