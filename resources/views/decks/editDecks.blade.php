@extends('layouts.app')

@section('content')
<div class="container">
    <select id="selectDeck" onchange="updateDeck()">
        @if(count($decks) > 0)
            @foreach($decks as $deck)
                <option id="{{$deck->id}}" value="{{$deck->id}}">{{$deck->name}}</option>
            @endforeach
        @endif
    </select>
    <button class="btn btn-danger" onclick="deleteDeck()">Delete this deck</button>
    <div id="displayCards">
        <select id = "selectCard" onchange="selectCard(this)" multiple>;
        </select>
    </div>

    <div id="addCard">
        <button class="btn btn-primary" onclick="toggleModalCard()">Add a new card</button>
    </div>
    <div id="modal-card" class="modal">
        {{ Form::open(array('onsubmit' => 'addCard(this); return false;', 'class' =>'modal-content')) }}
            <div class="form-group">
                {{ Form::label('front', 'Front') }}
                {{ Form::text('front', '', ['class' => 'form-control', 'placeholder' => 'Front']) }}
            </div>
            <div class="form-group">
                {{ Form::label('back', 'Back') }}
                {{ Form::text('back', '', ['class' => 'form-control', 'placeholder' => 'Back']) }}
            </div>
            {{  Form::submit('Add card', ['class' => 'btn btn-primary']) }}
        {{  Form::close() }}
    </div>
    <div id="deleteCard">
        <button class="btn btn-danger" onclick="deleteCard()">Delete selected card</button>
    </div>
    <div id="view-card">
        <div class="card">
            front of card
        </div>
        <div class="card">
            backside of card
        </div>
        <button class="btn btn-primary">edit</button>
    </div>
</div>


<!------- javascript ------------------------------------------------------->
<script type="application/javascript">
    let decks = {!!$decks!!};
    let deck, cards;
    updateDeck();
    
    function updateDeck(){
        deck = getCurrentDeck();
        updateCards();
    }
    function getCurrentDeck(){
        let e = document.getElementById('selectDeck');
        let deckId = e.options[e.selectedIndex].id
        for(let key in decks){
            if(decks[key].id == deckId){
                return decks[key];
            }
        }
    }

    function updateDecks(){
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            //on success
            if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            if(this.responseText)
                decks = JSON.parse(this.responseText);
                displayDecks();
                updateDeck();
                displayCards();
            }
        };
        //gets the url via hack
        xhttp.open('GET', '{!! route('decks.index')!!}/getDecks', true);
        xhttp.send();
    }

    function updateCards(){
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            //on success
            if (this.readyState == 4 && this.status == 200) {
            cards = JSON.parse(this.responseText);
            displayCards();
            }
        };
        //gets the url via hack
        xhttp.open('GET', '{!! route('decks.index')!!}'+`/${deck.id}`, true);
        xhttp.send();
    }

    function displayDecks(){
        let e = document.getElementById('selectDeck');
        e.innerHTML = '';
        for(let key in decks){
            e.innerHTML += `<option id="${decks[key].id}" value="${decks[key].id}">${decks[key].name}</option>`;
        }
    }

    function displayCards(){
        let e = document.getElementById('selectCard');
        e.innerHTML = '';
        for(let key in cards){
            e.innerHTML += `
            <option id="${cards[key].id}" value="${cards[key].id}">
                ${cards[key].front}/${cards[key].back} 
            </option>`;
        }
    }

    function selectCard(e){
        if(e.selectedOptions.length === 1){
            //view selected card
            //viewCard(e.selectedOptions[0].id);
            let card;
            let id = e.selectedOptions[0].id;
            for(let c of cards){
                if(c.id == id){
                    card = c;
                    break;
                }
            }
            let cardEl = document.getElementById('view-card');
            cardEl.childNodes[0].innerHTML = card.front;
            cardEl.childNodes[2].innerHTML = card.back;
            document.getElementById('front').innerHTML = card.front;
            document.getElementById('back').innerHTML = card.back;
        }
    }

    function deleteDeck(){
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            //on success
            if (this.readyState == 4 && this.status == 200) {
                let response = JSON.parse(this.responseText);
                console.log(response)
                updateDecks();
            }
        };
        //gets the url via hack
        xhttp.open('DELETE', '{!! route('decks.index')!!}'+`/${deck.id}`, true);
        let token =  document.querySelector('meta[name=csrf-token]').content
        xhttp.setRequestHeader('X-CSRF-Token', token);
        xhttp.send();
    }

    function deleteCard(){
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            //on success
            if (this.readyState == 4 && this.status == 200) {
                let response = JSON.parse(this.responseText);
                console.log(response)
                updateDecks();
            }
        };
        let e = document.getElementById('selectCard');
        let data = [];
        for(let opt of e.selectedOptions){
            data.push(parseInt(opt.value));
        }
        postData(`{!! route('cards.index')!!}/deleteMultiple`, {ids: data})
        .then(data => console.log(data)) // JSON-string from `response.json()` call
        .then( () => updateCards()) // JSON-string from `response.json()` call
        .catch(error => console.error(error));
        //gets the url via hack
       /* xhttp.open('DELETE', '{!! route('cards.index')!!}'+`/${deck.id}`, true);
        let token =  document.querySelector('meta[name=csrf-token]').content
        xhttp.setRequestHeader('X-CSRF-Token', token);
        xhttp.send();*/
    }

    function postData(url = ``, data = {}) {
        let token =  document.querySelector('meta[name=csrf-token]').content
  // Default options are marked with *
    return fetch(url, {
        method: "POST", // *GET, POST, PUT, DELETE, etc.
        headers: {
            'Content-Type': 'application/json',
             'X-CSRF-Token': token
        },
        body: JSON.stringify(data), // body data type must match "Content-Type" header
    })
    .then(response => response.json()); // parses response to JSON
}

    function toggleModalCard(){
        let e = document.getElementById('modal-card');
        e.style.display = e.style.display ==='block' ? 'none' : 'block';
    }

    function addCard(form){
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            //on success
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText); 
                form.reset(); 
                toggleModalCard();
                updateCards();
            }
        };
        data = "?"+"front="+form["front"].value+"&" + "back=" + form["back"].value
             + "&" + "id=" + deck.id;
        xhttp.open('POST', '{!! route('cards.index')!!}'+ data, true);
        let token =  document.querySelector('meta[name=csrf-token]').content
        xhttp.setRequestHeader('X-CSRF-Token', token);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send();
    }
</script>
@endsection