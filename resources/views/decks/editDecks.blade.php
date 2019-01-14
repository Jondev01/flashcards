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
    <div id="number-of-decks">
        hallo
    </div>
    <button class="btn btn-danger" onclick="deleteDeck()">Delete this deck</button>
    <div id="displayCards">
        <div id="number-of-cards">
        </div>
        <select id = "selectCard" onchange="selectCard(this)" multiple>;
        </select>
    </div>

    <div id="addDeck">
        <button class="btn btn-primary" onclick="toggleModalCard('modal-addDeck')">Add a new deck</button>
    </div>

    <div id="modal-addDeck" class="modal">
        {{ Form::open(array('onsubmit' => 'addDeck(this); return false;', 'class' =>'modal-content')) }}
            <div class="form-group">
                {{ Form::label('name', 'Name') }}
                {{ Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'German']) }}
            </div>
            <div class="form-group">
                {{ Form::label('description', 'Description') }}
                {{ Form::text('description', '', ['class' => 'form-control', 'placeholder' => 'Contains flashcards to practice German']) }}
            </div>
            {{  Form::submit('Add deck', ['class' => 'btn btn-primary']) }}
        {{  Form::close() }}
    </div>

    <div id="addCard">
        <button class="btn btn-primary" onclick="toggleModalCard('modal-card')">Add flashcard</button>
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
    <div id="modal-edit" class="modal">
        {{ Form::open(array('onsubmit' => 'editCard(this); return false;', 'class' =>'modal-content')) }}
            <div class="form-group">
                {{ Form::label('front', 'Front') }}
                {{ Form::text('front', '', ['class' => 'form-control', 'placeholder' => 'Front']) }}
            </div>
            <div class="form-group">
                {{ Form::label('back', 'Back') }}
                {{ Form::text('back', '', ['class' => 'form-control', 'placeholder' => 'Back']) }}
            </div>
            {{  Form::submit('Save changes', ['class' => 'btn btn-primary']) }}
        {{  Form::close() }}
    </div>
    <div id="deleteCard">
        <button class="btn btn-danger" onclick="deleteCard()">Delete selected card(s)</button>
    </div>
    <div id="view-card">
        <div id="front" class="card">
            front of card
        </div>
        <div id="back" class="card">
            backside of card
        </div>
        <div id="editCard">
            <button class="btn btn-primary" onclick="toggleModalCard('modal-edit')">edit</button>
        </div>
    </div>
</div>


<!------- javascript ------------------------------------------------------->
<script type="application/javascript">
    let decks = {!!$decks!!};
    let deck, cards, curCard;
    updateDecks();
    
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
        //display number of decks
        document.getElementById('number-of-decks').innerHTML = `${Object.keys(decks).length} deck${Object.keys(decks).length !== 1 ? "s" : ""}`;
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
        //number of cards
        if(cards)
            document.getElementById('number-of-cards').innerHTML = `${Object.keys(cards).length} flashcard${Object.keys(cards).length !== 1 ? "s" : ""}`;
        else 
        document.getElementById('number-of-cards').innerHTML = "0 cards";

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
            curCard = card;
            let cardEl = document.getElementById('view-card');
            cardEl.dataset.value = id;
            cardEl.children[0].innerHTML = card.front;
            cardEl.children[1].innerHTML = card.back;
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

    function toggleModalCard(id){
        let e = document.getElementById(id);
        if(id === 'modal-edit'){
            //if edit, display current values
            e.children[0]["front"].value = curCard.front;
            e.children[0]["back"].value = curCard.back;
        }
        e.style.display = e.style.display ==='block' ? 'none' : 'block';
    }

    function addCard(form){
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            //on success
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText); 
                form.reset(); 
                toggleModalCard('modal-card');
                updateCards();
            }
        };
        let data = "?"+"front="+form["front"].value+"&" + "back=" + form["back"].value
             + "&" + "id=" + deck.id;
        xhttp.open('POST', '{!! route('cards.index')!!}'+ data, true);
        let token =  document.querySelector('meta[name=csrf-token]').content
        xhttp.setRequestHeader('X-CSRF-Token', token);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send();
    }

    function addDeck(form){
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            //on success
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText); 
                form.reset(); 
                toggleModalCard('modal-addDeck');
                updateDecks();
            }
        };
        let data = "?"+"name="+form["name"].value+"&" + "description=" + form["description"].value;
        xhttp.open('POST', '{!! route('decks.index')!!}'+ data, true);
        let token =  document.querySelector('meta[name=csrf-token]').content
        xhttp.setRequestHeader('X-CSRF-Token', token);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send();
    }

    function editCard(form){
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            //on success
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText); 
                form.reset(); 
                toggleModalCard('modal-edit');
                updateCards();
                document.getElementById('front').innerHTML = '';
                document.getElementById('back').innerHTML = '';
            }
        };
        let data = "?"+"front="+form["front"].value+"&" + "back=" + form["back"].value
             + "&" + "id=" + deck.id;
        let card_id = document.getElementById('view-card').dataset.value;
        xhttp.open('PUT', '{!! route('cards.index')!!}'+ `/${card_id}`+data, true);
        let token =  document.querySelector('meta[name=csrf-token]').content
        xhttp.setRequestHeader('X-CSRF-Token', token);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send();
    }
</script>
@endsection