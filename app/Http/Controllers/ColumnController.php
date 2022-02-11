<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\Column;

class ColumnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $columns = Column::oldest()->get();

        return response()->json([
            'columns' => $columns
        ]);
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
        ]);

        if ($validator->fails()) { 
            return \Response::json(array(
                'errors' => Arr::flatten($validator->errors()->toArray())
            ), 422);
        }

        $column = new Column();
        $column->title = $request->title;
        $column->save();

        return response()->json([
            'message' => 'Column has been added successfully!'
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
        $column = Column::findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'title'=>'required|string',
        ]);

        if ($validator->fails()) { 
            return \Response::json(array(
                'errors' => Arr::flatten($validator->errors()->toArray())
            ), 422);
        }

        $column->title = $request->title;
        $column->position = $request->position;
        $column->save();

        return response()->json([
            'message' => 'Column has been updated successfully!'
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
        $column = Column::findOrFail($id);
        $column->delete();

        return response()->json([
            'message' => 'Column has been deleted successfully!'
        ]);
    }
}
