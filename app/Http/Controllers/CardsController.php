<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Card;

class CardsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cards.create');
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
            'front' => 'required',
            'back' => 'required',
            'id' => 'required'
        ]);

        $card = new Card;
        $card->fill([
            'front' => $request->input('front'),
            'back'=> $request->input('back')
        ]);
        $card->deck_id = $request->input('id');
        $card->save();
        $request->session()->flash('success', 'Card saved!');
        return view('layouts.messages');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $this->validate($request, [
            'front' => 'required',
            'back' => 'required',
        ]);
        $card = Card::findOrFail($id);
        $card->front = $request->input('front');
        $card->back = $request->input('back');
        $card->save();
        $request->session()->flash('success', 'The card was updated!');
        return view('layouts.messages');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
            Card::findOrFail($id)->delete();
    }

    public function deleteMultiple(Request $request){
        Card::destroy($request->input('ids'));
        $request->session()->flash('success','Card(s) deleted!');
        return response()->json([
            'body' => view('layouts.messages')->render(),
        ]);
    }
}
