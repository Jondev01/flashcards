@extends('layouts.app')

@section('content')
<div class="container">
    <div id="displayDecks">
        <div>
            <h4>Your Decks</h4>
            <div id="number-of-decks">
                    0 decks
            </div>
            <div id="addDeck">
                    <i class="fa fa-plus" onclick="toggleModalCard('modal-addDeck')" data-toggle="tooltip" title="Create new deck"></i>
            </div>
            <i class="fa fa-trash" onclick="deleteDeck()" aria-hidden="true" data-toggle="tooltip" title="Delete selected deck"></i>
        </div>
        <select id="selectDeck" onchange="updateDeck()">
            @if(count($decks) > 0)
                @foreach($decks as $deck)
                    <option id="{{$deck->id}}" value="{{$deck->id}}">{{$deck->name}}</option>
                @endforeach
            @endif
        </select>
    </div>
    <div id="displayCards">
        <h5>Flashcards</h5>
        <div>
            <div id="number-of-cards">
                0 flashcards
            </div>
            <div id="addCard">
                    <i class="fa fa-plus" onclick="toggleModalCard('modal-card')" data-toggle="tooltip" title="Create new flashcard"></i>
            </div>
            <i class="fa fa-trash" onclick="deleteCard()" aria-hidden="true" data-toggle="tooltip" title="Delete selected flashcard(s)"></i>
        </div>
        <select id = "selectCard" class="select-multiple" onchange="selectCard(this)" multiple>;
        </select>
            
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

    <div id="modal-card" class="modal">
        {{ Form::open(array('onsubmit' => 'addCard(this); return false;', 'class' =>'modal-content')) }}
            <div class="form-group">
                {{ Form::label('front', 'Front') }}
                {{ Form::text('front', '', ['class' => 'form-control', 'placeholder' => 'hallo']) }}
            </div>
            <div class="form-group">
                {{ Form::label('back', 'Back') }}
                {{ Form::text('back', '', ['class' => 'form-control', 'placeholder' => 'hello']) }}
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
    <div id="view-card" class="card">
        <div class="card-body">
            <p id="view-front" class="card-text">
            </p>
            <hr>
            <p id="view-back" class="card-text">
            </p>
        </div>  
        <div id="editCard">
            <button class="btn btn-primary" onclick="toggleModalCard('modal-edit')" data-toggle="tooltip" title="Edit card"><i class="fa fa-edit"></i></button>
        </div>
    </div>
</div>


<!------- javascript ------------------------------------------------------->
<script type="application/javascript">
//close Modals
window.onclick = function(event) {
    let modals = [
        document.getElementById('modal-edit'),
        document.getElementById('modal-card'),
        document.getElementById('modal-addDeck')
        ];
    for(let modal of modals){
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
}

    let decks = {!!$decks!!};
    let deck, cards, curCard;
    updateDecks();
    
    function updateDeck(){
        if(decks.length>0)
            deck = getCurrentDeck();
        else 
            deck = undefined;
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
                decks = JSON.parse(this.responseText);
                displayDecks();
                updateDeck();
                displayCards();
            }
        };
        //gets the url via hack
        xhttp.open('GET', '{!! route('decks.getDecks')!!}', true);
        xhttp.send();
    }

    function updateCards(){
        document.getElementById('view-front').innerHTML = '';
        document.getElementById('view-back').innerHTML = '';
        delete document.getElementById('view-card').dataset.value;
        if(deck)
            fetch(`{!! route('decks.index')!!}/${deck.id}`)
            .then( response => response.json())
            .then( (data) => {
                cards = data;
                displayCards();
            });
        else {
            cards = undefined;
            displayCards();
        }
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
            document.getElementById('view-front').innerHTML = curCard.front;
            document.getElementById('view-back').innerHTML = curCard.back;
        }
    }

    function deleteDeck(){
        if(decks.length == 0)
            return;
        if(!confirm(`Do you want to delete the deck \'${deck.name}\'?`))
            return;
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            //on success
            if (this.readyState == 4 && this.status == 200) {
                flashMessage(this.responseText);
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
        if(!confirm('Delete the selected card(s)?'))
            return;
        
        let e = document.getElementById('selectCard');
        let data = [];
        for(let opt of e.selectedOptions){
            data.push(parseInt(opt.value));
        }
        postData(`{!! route('cards.index')!!}/deleteMultiple`, {ids: data})
        .then(response => flashMessage(response["body"])) // JSON-string from `response.json()` call
        .then( () => updateCards()) 
        .catch(error => console.error(error));
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

    function flashMessage(msg){
        let el = document.getElementById('flash-message');
        el.innerHTML += msg;
       setTimeout(() => el.removeChild(el.childNodes[0]), 5000);
    }

    function toggleModalCard(id){
        let e = document.getElementById(id);
        if(id === 'modal-edit'){
            if(!curCard)
                return;
            //if edit, display current values
            e.children[0]["front"].value = curCard.front;
            e.children[0]["back"].value = curCard.back;
        }
        e.style.display = e.style.display ==='block' ? 'none' : 'block';
    }

    function addCard(form){
        if(decks.length == 0)
            return;
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            //on success
            if (this.readyState == 4 && this.status == 200) {
                flashMessage(this.responseText);
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
                flashMessage(this.responseText);
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
                flashMessage(this.responseText); 
                form.reset(); 
                toggleModalCard('modal-edit');
                updateCards();
                //now in updateCards()
                /*document.getElementById('view-front').innerHTML = '';
                document.getElementById('view-back').innerHTML = '';
                delete document.getElementById('view-card').dataset.value;*/
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