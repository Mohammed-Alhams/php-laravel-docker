<?php

namespace App\Http\Controllers\Api\Pharmacy;

use App\Http\Controllers\Api\BaseController;
use App\Models\Stock;
use Illuminate\Http\Request;
use Validator;

class StockController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $stocks = Stock::filter($request->query())->paginate();
        return $this->sendResponse($stocks, "Stocks retrieved successfully.");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|string',
            'id' => 'required|numeric|unique:stocks',
            'unit_price' => 'required|numeric',
            'box_price' => 'required|numeric',
            'box_wholesale_price' => 'required|numeric',
            'unit_wholesale_price' => 'required|numeric',
            'quantity_by_boxes' => 'required|numeric',
            'quantity_by_units' => 'required|numeric',
            'pharmacy_id' => 'required|numeric|exists:stores,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $stock = Stock::create($request->all());

        return $this->sendResponse($stock, "Stock created successfully");
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
        $stock = Stock::find($id);
        if (is_null($stock)) {
            return $this->sendError('Stock not found.');
        }

        return $this->sendResponse($stock, "Stock retrieved successfully.");
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
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|max:255|string',
            'id' => 'sometimes|required|max:255|string',
            'unit_price' => 'sometimes|required|numeric',
            'box_price' => 'sometimes|required|numeric',
            'box_wholesale_price' => 'sometimes|required|numeric',
            'unit_wholesale_price' => 'sometimes|required|numeric',
            'quantity_by_boxes' => 'sometimes|required|numeric',
            'quantity_by_units' => 'sometimes|required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $stock->update($request->all());

        return $this->sendResponse($stock, "Stock updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $barcode
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        return $this->sendResponse(Stock::destroy($id), "Stock deleted successfully");
    }
}
