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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'access_token' => 'required|string',
            'date' => 'nullable|date:Y-m-d',
            'status' => 'nullable|boolean',
        ]);

        if ($validator->fails() || DB::table('personal_access_tokens')->where('token', $request->access_token)->doesntExist()) { 
            return response()->json([]);
        }

        $cards = Card::latest();
        
        if ($request->has('date')) {
            $cards->whereDate('created_at', $request->date);
        }

        if ($request->has('status')) {
            if ($request->status == 0) {
                $cards->onlyTrashed();
            }
        } else {
            $cards->withTrashed();
        }

        return response()->json($cards->get()->makeHidden(['order', 'column_id', 'show_modal', 'update_path', 'delete_path', 'updated_at']));
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
            'order'=>'nullable|integer',
        ]);

        if ($validator->fails()) { 
            return \Response::json(array(
                'errors' => Arr::flatten($validator->errors()->toArray())
            ), 422);
        }

        $maxOrder = Card::where('column_id', $request->column_id)->max('order');

        $card = new Card();
        $card->title = $request->title;
        $card->column_id = $request->column_id;
        $card->order = is_null($maxOrder) ? 0 : ++$maxOrder;
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
            'order'=>'nullable|integer',
        ]);

        if ($validator->fails()) { 
            return \Response::json(array(
                'errors' => Arr::flatten($validator->errors()->toArray())
            ), 422);
        }

        $card->title = $request->title;
        $card->description = $request->description;
        $card->column_id = $request->column_id;
        $card->order = $request->order;
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
