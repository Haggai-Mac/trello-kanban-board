<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Column;
use Illuminate\Support\Facades\DB;

class CardController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'title'=>'required|string',
            'column_id'=>'required|integer',
            'description'=>'nullable|string',
            'position'=>'nullable|integer',
        ]);

        if ($validator->fails()) { 
            return \Response::json(array(
                'errors' => Arr::flatten($validator->errors()->toArray())
            ), 422);
        }

        $card = new Card();
        $card->title = $request->title;
        $card->column_id = $request->column_id;
        $card->save();

        $Column = Column::find($request->column_id);

        return response()->json([
            'cards' => $Column->cards,
            'message' => 'Card has been added successfully!'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $card = Card::findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'title'=>'required|string',
            'column_id'=>'required|integer',
            'description'=>'nullable|string',
            'position'=>'nullable|integer',
        ]);

        if ($validator->fails()) { 
            return \Response::json(array(
                'errors' => Arr::flatten($validator->errors()->toArray())
            ), 422);
        }

        $card->title = $request->title;
        $card->description = $request->description;
        $card->column_id = $request->column_id;
        $card->position = $request->position;
        $card->save();

        return response()->json([
            'message' => 'Card has been updated successfully!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $card = Card::findOrFail($id);
        $card->delete();

        return response()->json([
            'message' => 'Card has been deleted successfully!'
        ]);
    }
}
