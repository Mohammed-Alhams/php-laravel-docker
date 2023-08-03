<?php

namespace App\Http\Controllers\Api\Pharmacy;

use App\Http\Controllers\Api\BaseController;
use App\Models\Invoice;
use App\Models\InvoiceStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        DB::beginTransaction();

        try{
            $invoice = Invoice::create(
                [
                    'invoice_no' => $request->invoice_no,
                    'description' => $request->description,
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
        }catch (\Exception $exception){
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
    public function update(Request $request, $id)
    {
        //
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
