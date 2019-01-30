@extends('layouts/app')

@section('content')
    <div class="container">
            @if(count($decks) > 0)
                <select id="selectDeck" onchange="updateDeck()">
                @foreach($decks as $deck)
                    <option id="{{$deck->id}}" value="{{$deck->id}}">{{$deck->name}}</option>
                @endforeach
                </select>
            @elseif(count($decks) == 0)
                <div style="text-align: center; font-size: 2rem;">
                    <a href="{{route('decks.editDecks')}}">Create a Deck!</a>
                </div>
                @endif
        <!--<button onclick="nextCard()">Next</button>-->
        <div class="card noselect fc-pointer" onclick="flipCard()" style="height:40vh">
            <div class="card-body">
                <p id="card" class="card-text">Create a deck of flashcards, then come back to practice!</p>
                <hr>
                <p id="card-back" class="card-text"></p>
            </div>
        </div>
    </div>

    <!------- javascript --------->
    <script type="application/javascript">
        let decks = {!!$decks!!};
        let deck, cards, card;
        let front = true;
        if(decks.length)
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
            if(Object.keys(cards).length==0){
                document.getElementById("card").innerHTML = "<a href=\"{{route('decks.editDecks')}}\">Add flashcards to this deck</a>";
                document.getElementById("card-back").innerHTML = ""
                card = undefined;
                return;
            }
            //get random card
            let temp = cards[Math.floor(Math.random()*cards.length)]; 
            //makes sure a new card was chosen, otherwise try again
            if(card && card.id === temp.id && cards.length>1)
                nextCard();
            else
                card = temp;
            front = true;
            document.getElementById("card").innerHTML = card.front;
            document.getElementById("card-back").innerHTML = ""
        }

        function flipCard(){
            if(!card)
                return;
            if(front){
                document.getElementById("card-back").innerHTML = card.back;
                front = false;
            }
            else{ 
                nextCard();
                /*document.getElementById("card").innerHTML = card.front;
                document.getElementById("card-back").innerHTML = "";*/
            }
        }

    </script>
@endsection
