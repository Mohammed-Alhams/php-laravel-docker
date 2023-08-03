<?php

namespace App\Http\Controllers\Api\Pharmacy;

use App\Http\Controllers\Api\BaseController;
use App\Models\Invoice;
use App\Models\InvoiceStock;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Array_;

class CheckoutController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $invoices = Invoice::with('stocks')->paginate();

        return $this->sendResponse($invoices, "Invoices retrieved successfully.");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = \Validator::make($request->all(), [
            'stocks' => ['required', 'array'],
            'stocks.*.id' => ['required', 'int', 'exists:stocks,id'],
            'stocks.*.quantity' => ['required', 'int', 'min:1'],
            'invoice_no' => ['required', 'string', 'unique:invoices,invoice_no'],
        ]);

        //check if quantity is in stock
        if ($request->stocks){
            foreach ($request->stocks as $stock) {
                $stockModel = Stock::find($stock['id']);
                if ($stockModel['quantity_by_units'] < $stock['quantity']){
                    return $this->sendError('Validation Error.', 'Quantity is not in stock');
                }
            }
        }

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        $totalPrice = 0;

        foreach ($request->stocks as $stock) {
            $stockModel = Stock::find($stock['id']);
            $totalPrice += ($stockModel['unit_price'] * $stock['quantity']);
            $stockModel->update([
                'quantity_by_units' => (($stockModel['quantity_by_units'] - $stock['quantity'])),
            ]);
        }

        try {
            $invoice = Invoice::create(
                [
                    'invoice_no' => $request->invoice_no,
                    'description' => $request->description,
                    'total_price' => $totalPrice,
                ]
            );

            foreach ($request->stocks as $stock) {
                InvoiceStock::create(
                    [
                        'invoice_id' => $invoice->id,
                        'stock_id' => $stock['id'],
                        'quantity' => $stock['quantity'],
                    ]
                );
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

        return $this->sendResponse($invoice, "Invoice created successfully");
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
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $invoiceNumber)
    {
        //
        $validator = \Validator::make($request->all(), [
            'stocks' => ['sometimes', 'required', 'array'],
            'stocks.*.id' => ['sometimes', 'required', 'int', 'exists:stocks,id'],
            'stocks.*.quantity' => ['sometimes', 'required', 'int', 'min:1'],
        ]);

        //check if quantity is in stock
        if ($request->stocks){
            foreach ($request->stocks as $stock) {
                $stockModel = Stock::find($stock['id']);
                if ($stockModel['quantity_by_units'] < $stock['quantity']){
                    return $this->sendError('Validation Error.', 'Quantity is not in stock');
                }
            }
        }

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        $totalPrice = 0;

        if ($request->stocks){
            foreach ($request->stocks as $stock) {
                $stockModel = Stock::find($stock['id']);
                $totalPrice += ($stockModel['unit_price'] * $stock['quantity']);
                $stockModel->update([
                    'quantity_by_units' => ($stockModel['quantity_by_units'] - $stock['quantity']),
                ]);
            }
        }

        $invoice = Invoice::where('invoice_no', $invoiceNumber)->first();

        try {
            $invoice->update(
                [
                    'invoice_no' => $invoiceNumber,
                    'description' => $request->description,
                    'total_price' => $totalPrice,
                ]
            );

            $invoiceStock = InvoiceStock::where('invoice_id', $invoice->id)->first();
            if ($request->stocks){
                foreach ($request->stocks as $stock) {
                    $invoiceStock->update(
                        [
                            'invoice_id' => $invoice->id,
                            'stock_id' => $stock['id'],
                            'quantity' => $stock['quantity'],
                        ]
                    );
                }
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

        return $this->sendResponse($invoice, "Invoice updated successfully");
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
        return $this->sendResponse(Invoice::destroy($id), "Invoice deleted successfully");
    }
}
