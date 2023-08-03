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

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        $totalPrice = 0;

        foreach ($request->stocks as $stock) {
            $stockModel = Stock::find($stock['id']);
            $totalPrice += ($stockModel['unit_price'] * $stock['quantity']);
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
        print $invoiceNumber;

        $validator = \Validator::make($request->all(), [
            'stocks' => ['sometimes', 'required', 'array'],
            'stocks.*.id' => ['sometimes', 'required', 'int', 'exists:stocks,id'],
            'stocks.*.quantity' => ['sometimes', 'required', 'int', 'min:1'],
            'invoice_no' => ['sometimes', 'required', 'string', 'unique:invoices,invoice_no'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        $totalPrice = 0;

        foreach ($request->stocks as $stock) {
            $stockModel = Stock::find($stock['id']);
            $totalPrice += ($stockModel['unit_price'] * $stock['quantity']);
        }

        $invoice = Invoice::where('invoice_no', $invoiceNumber)->first();

        try {
            $invoice->update(
                [
                    'invoice_no' => $request->invoice_no,
                    'description' => $request->description,
                    'total_price' => $totalPrice,
                ]
            );

            $invoiceStock = InvoiceStock::where('invoice_id', $invoice->id)->first();
            foreach ($request->stocks as $stock) {
                $invoiceStock->update(
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
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
