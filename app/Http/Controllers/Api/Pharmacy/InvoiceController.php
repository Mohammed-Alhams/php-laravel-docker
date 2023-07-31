<?php

namespace App\Http\Controllers\Api\Pharmacy;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Stock;
use Illuminate\Http\Request;
use Validator;

class InvoiceController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $stocks = Invoice::filter($request->query())->paginate();

        return $this->sendResponse($stocks, "Invoices retrieved successfully.");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $stock = Stock::where('barcode', '=', $input['barcode'])->first();

        if (is_null($stock)){
            return $this->sendError("Stock doesn't exist.");
        }

        $input['stock_id'] = $stock->id;
        $validator = Validator::make($input, [
            'quantity' => ['required', 'nullable', 'int', 'min:1'],
            'invoice_no' => ['required', 'int'],
            'barcode' => ['required', 'int'],
        ]);

        if ($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $invoice = Invoice::create($input);

        return $this->sendResponse($invoice, "Invoice created successfully");
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
        $invoice = Invoice::find($id);
        if (is_null($invoice)){
            return $this->sendError('Stock not found.');
        }

        return $this->sendResponse($invoice, "Stock retrieved successfully.");

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
        $validator = Validator::make($request->all(), [
            'stock_id' => ['sometimes', 'required', 'int', 'exists:stocks,id'],
            'pharmacist_id' => ['sometimes', 'required', 'int', 'exists:pharmacists,id'],
            'quantity' => ['sometimes', 'nullable', 'int', 'min:1'],
        ]);

        if ($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $invoice->update($request->all());

        return $this->sendResponse($invoice, "Invoice created successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        return $this->sendResponse(Invoice::destroy($id), "Invoice deleted successfully");
    }
}
