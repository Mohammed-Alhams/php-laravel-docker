<?php

namespace App\Http\Controllers\Api\Pharmacy;

use App\Http\Controllers\Api\BaseController;
use App\Models\Stores;
use Illuminate\Http\Request;
use Validator;

class PharmacyController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return $this->sendResponse(Stores::paginate(), "Pharmacies retrieved successfully.");
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $pharmacy = Stores::create($request->all());

        return $this->sendResponse($pharmacy, "Stores created successfully");
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
        $pharmacy = Stores::find($id);
        if (is_null($pharmacy)) {
            return $this->sendError('Stores not found.');
        }

        return $this->sendResponse($pharmacy, "Stock retrieved successfully.");
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
        return $this->sendResponse(Stores::destroy($id), "Stores deleted successfully");
    }
}
