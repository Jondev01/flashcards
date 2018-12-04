<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Deck;
use Auth;

class DecksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $decks = Auth::user()->decks->sortByDesc('name');
        return view('decks.index')->with('decks', $decks);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
    }
    public function getDecks()
    {
        $decks = Auth::user()->decks->sortByDesc('name');
        return response()->json($decks);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('decks.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required'
        ]);

        $deck = new Deck;
        $deck->fill([
            'name' => $request->input('name'),
            'description'=> $request->input('description'),
        ]);
        $deck->user_id = Auth::id();
        $deck->save();
        return redirect('home');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $deck = Deck::findOrFail($id);
        return response()->json($deck->cards);
    }

    public function editDecks()
    {
        $decks = Auth::user()->decks->sortByDesc('name');
        return view('decks.editDecks')->with('decks', $decks);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $deck = Deck::findOrFail($id);
        return view('decks.edit')->with('deck', $deck);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Deck::destroy($id);
        return response()->json(['success' => 'The deck was successfully deleted']);
    }
}
