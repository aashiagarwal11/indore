<?php

namespace App\Http\Controllers;

use App\Models\SaleProductList;
use App\Models\SaleSubCategory;
use App\Models\SaleSubCategoryProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Sale;
use Illuminate\Support\Facades\Schema;

class SaleSubCategoryProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ## all list of selling product form from user side which is accepted by admin we show this list on user panel.

        try {
            $sellingproduct = SaleSubCategoryProduct::where('status', 1)->get()->toArray();
            if (!empty($sellingproduct)) {
                return response()->json([
                    'message' => 'List of product want to sell',
                    'data' => $sellingproduct,
                ]);
            } else {
                return response()->json([
                    'message' => 'No record exist',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $auth_id = auth()->user()->id;


        // $validator = Validator::make($request->all(), [
        //     'sub_cat_id '       => ['required', 'numeric'],
        //     'vendor_name'       => ['required', 'alpha', 'string', 'max:50'],
        //     'owner_or_broker'   => ['required', 'alpha', 'string', 'max:255', 'in:owner,broker'],
        //     'vehicle_sighting'  => ['required', 'string', 'max:255'],
        //     'property_location' => ['required', 'string', 'max:255'],
        //     'price'             => ['required'],
        //     'brand'             => ['required', 'string', 'max:30'],
        //     'model_name'        => ['required', 'string', 'max:30'],
        //     'model_year'        => ['required', 'numeric', 'max:20'],
        //     'fuel_type'         => ['required', 'string', 'max:20'],
        //     'seater'            => ['required', 'numeric', 'max:30'],
        //     'kilometer_running' => ['required', 'string', 'max:30'],
        //     'insurance_period'  => ['required', 'string', 'max:20'],
        //     'color'             => ['required', 'alpha', 'string', 'max:20'],
        //     'other_information' => ['required', 'alpha', 'string', 'max:255'],
        //     'size_length_width' => ['required', 'alpha', 'string', 'max:255'],
        // ]);
        try {
            if ($auth_id != 1) {
                $validator = Validator::make($request->all(), [
                    'sale_id' => ['required', 'numeric'],
                ]);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()]);
                }

                $sell_id = $request->sale_id;

                $chksellid = Sale::where('id', $sell_id)->first();
                if (!empty($chksellid)) {
                    $validator = Validator::make($request->all(), [
                        'sub_cat_id'       => ['required', 'numeric'],
                        'vendor_name'       => ['required', 'alpha', 'string', 'max:50'],
                        'owner_or_broker'   => ['required', 'alpha', 'string', 'max:255', 'in:owner,broker'],
                        'property_location' => ['required', 'string', 'max:255'],
                        'price'             => ['required'],
                    ]);
                    if ($validator->fails()) {
                        return response()->json(['message' => $validator->errors()]);
                    }

                    if (!empty($chksellid->type)) {
                        if ($chksellid->type == 'vehicle') {
                            $validator = Validator::make($request->all(), [
                                'vehicle_sighting'  => ['required', 'string', 'max:255'],
                                'brand'             => ['required', 'string', 'max:30'],
                                'model_name'        => ['required', 'string', 'max:20'],
                                'model_year'        => ['required', 'numeric'],
                                'fuel_type'         => ['required', 'string', 'max:20'],
                                'seater'            => ['required', 'numeric', 'max:30'],
                                'kilometer_running' => ['required', 'string', 'max:30'],
                                'insurance_period'  => ['required', 'string', 'max:20'],
                                'color'             => ['required', 'alpha', 'string', 'max:20'],
                            ]);
                            if ($validator->fails()) {
                                return response()->json(['message' => $validator->errors()]);
                            }
                            $sellproduct = SaleSubCategoryProduct::create([
                                'sale_id' => $sell_id,
                                'sub_cat_id' => $request->sub_cat_id,
                                'vendor_name' => $request->vendor_name,
                                'owner_or_broker' => $request->owner_or_broker,
                                'property_location' => $request->property_location,
                                'price' => $request->price,
                                'vehicle_sighting' => $request->vehicle_sighting,
                                'brand' => $request->brand,
                                'model_name' => $request->model_name,
                                'model_year' => $request->model_year,
                                'fuel_type' => $request->fuel_type,
                                'seater' => $request->seater,
                                'kilometer_running' => $request->kilometer_running,
                                'insurance_period' => $request->insurance_period,
                                'color' => $request->color,
                                'other_information' => $request->other_information ?? null,
                            ]);
                            return response()->json([
                                'message' => 'Product added successfully in sell list',
                                'data' => $sellproduct,
                            ]);
                        } elseif ($chksellid->type == 'property') {
                            $validator = Validator::make($request->all(), [
                                'size_length_width'  => ['required', 'number'],
                            ]);
                            if ($validator->fails()) {
                                return response()->json(['message' => $validator->errors()]);
                            }
                            $sellproduct = SaleSubCategoryProduct::create([
                                'sale_id' => $sell_id,
                                'sub_cat_id' => $request->sub_cat_id,
                                'vendor_name' => $request->vendor_name,
                                'owner_or_broker' => $request->owner_or_broker,
                                'property_location' => $request->property_location,
                                'price' => $request->price,
                                'size_length_width' => $request->size_length_width,
                                'other_information' => $request->other_information ?? null,
                            ]);
                            return response()->json([
                                'message' => 'Product added successfully in sell list',
                                'data' => $sellproduct,
                            ]);
                        }
                    } else {
                        return response()->json([
                            'message' => 'No sell type exist',
                        ]);
                    }
                } else {
                    return response()->json([
                        'message' => 'Sale id not exist',
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Sorry!! Only user can add details, login as user first',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SaleSubCategoryProduct $saleSubCategoryProduct)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SaleSubCategoryProduct $saleSubCategoryProduct)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SaleSubCategoryProduct $saleSubCategoryProduct)
    {
        //
    }

    public function sellFormListOfUser(Request $request)
    {
        ## all list of selling product form from user side we show this list on admin panel so that admin can accept and deny the product.
        try {
            $sellingproduct = SaleSubCategoryProduct::where('status', 0)->get()->toArray();
            if (!empty($sellingproduct)) {
                return response()->json([
                    'message' => 'List of product want to sell',
                    'data' => $sellingproduct,
                ]);
            } else {
                return response()->json([
                    'message' => 'No record exist',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function acceptDenySell(Request $request)
    {
        dd('acceptdenyproduct');
        ## request accept and deny by the admin for user sell form 
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'numeric', 'in:0,1'],
            'id' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        $getdata =  SaleSubCategoryProduct::all(); ## all data with containing 0 and 1 status
        dd($getdata);
        $id = $request->id;
        try {
            if (!empty($getdata)) {
                $eachnews = News::where('id', $id)->first();
                if (!empty($eachnews)) {
                    $updatecity = $eachnews->update([
                        'status' => $request->status,
                    ]);
                    $all = News::where('id', $id)->first();
                    if ($request->status == 1) {
                        return response()->json([
                            'message' => 'Accepted By Admin',
                            'data' => $all,
                        ]);
                    } else {
                        return response()->json([
                            'message' => 'Deny By Admin',
                            'data' => $all,
                        ]);
                    }
                } else {
                    return response()->json([
                        'message' => 'Record Not Exist',
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'No News Available',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
