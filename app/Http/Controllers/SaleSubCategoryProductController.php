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
        dd('index');
        try {
            $sale_product = SaleSubCategoryProduct::all();
            if (!empty($sale_product)) {
                return response()->json([
                    'message' => 'Sale Product List',
                    'data' => $sale_product,
                ]);
            } else {
                return response()->json([
                    'message' => 'No Sale Products Found',
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
        dd('store');
        $sa_id = auth()->user()->id;


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
            if ($sa_id != 1) {
                $validator = Validator::make($request->all(), [
                    'sale_id' => ['required', 'numeric'],
                ]);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()]);
                }

                $sale_id = $request->sale_id;

                $chksaleid = Sale::where('id', $sale_id)->first(); ## in sale table
                if (!empty($chksaleid)) {
                    // $chksale_id_pro = SaleProductList::where('sale_id', $sale_id)->pluck('field_name'); ## in sale_product_lists table
                    // $chk = $chksale_id_pro->toArray();
                    dd($request->all());
                    if (!empty($chk)) {
                        dd($chk);
                        $validator = Validator::make($chk, [
                            'sale_id' => ['required', 'numeric'],
                        ]);
                        if ($validator->fails()) {
                            return response()->json(['message' => $validator->errors()]);
                        }
                    } else {
                        return response()->json([
                            'message' => 'Value not exist',
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
        dd('stop');






        // $chksaletype = Sale::where('type', $request->type)->first();
        // if (empty($chksaletype)) {
        //     $saletype = Sale::create([
        //         'type' => $request->type,
        //     ]);
        //     return response()->json([
        //         'message' => 'Sale Type Added Successfully',
        //         'data' => $saletype,
        //     ]);
        // } else {
        //     return response()->json([
        //         'message' => 'Sale Type Already Exist',
        //     ]);
        // }
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
}
