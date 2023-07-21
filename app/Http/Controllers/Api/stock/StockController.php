<?php

namespace App\Http\Controllers\Api\stock;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Stock::paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|string',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'barcode' => 'required|max:255|string',
            'unit_price' => 'required|numeric',
            'box_price' => 'required|numeric',
            'box_wholesale_price' => 'required|numeric',
            'unit_wholesale_price' => 'required|numeric',
            'quantity_by_boxes' => 'required|numeric',
        ]);

        $stock = Stock::create($request->all());

        return Response::json($stock, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return Stock::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stock $stock)
    {
        //
        $request->validate([
            'name' => 'sometimes|required|max:255|string',
            'price' => 'sometimes|required|numeric',
            'quantity' => 'sometimes|required|numeric',
            'barcode' => 'sometimes|required|max:255|string',
            'unit_price' => 'sometimes|required|numeric',
            'box_price' => 'sometimes|required|numeric',
            'box_wholesale_price' => 'sometimes|required|numeric',
            'unit_wholesale_price' => 'sometimes|required|numeric',
            'quantity_by_boxes' => 'sometimes|required|numeric',
        ]);

        $stock->update($request->all());

        return Response::json($stock);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        Stock::destroy($id);
        return Response::json([
            'message' => 'Stock deleted successfully'
        ]);
    }
}
