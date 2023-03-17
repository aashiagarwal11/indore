<?php

namespace App\Http\Controllers;

use App\Models\SaleProductList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Sale;
use App\Models\SaleSubCategoryProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class SaleProductListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        ## storing product list
        $auth_id = auth()->user()->id;
        try {
            if ($auth_id == 1) {
                $chkSale_id = Sale::where('id', $request->sale_id)->first();
                if (!empty($chkSale_id)) {
                    $validator = Validator::make($request->all(), [
                        'sale_id'       => ['required', 'numeric'],
                        'field_name'  => ['required', 'string', 'max:50'],
                    ]);
                    if ($validator->fails()) {
                        return response()->json(['message' => $validator->errors()]);
                    }
                    $existcolumnfield = SaleProductList::where('sale_id', $request->sale_id)->where('field_name', $request->field_name)->first();
                    if (empty($existcolumnfield)) {
                        $saletype = SaleProductList::create([
                            'sale_id' => $request->sale_id,
                            'field_name' => $request->field_name,
                        ]);
                    } else {
                        return response()->json([
                            'message' => 'Column exist with same sale id',
                        ]);
                    }

                    $columns = Schema::getColumnListing('sale_sub_category_products');
                    $getcolumn = in_array($request->field_name, $columns);

                    if ($getcolumn == false) {
                        $fieldInsertInTable = DB::select("ALTER TABLE sale_sub_category_products
                        ADD $request->field_name VARCHAR(255) null");
                    } else {
                        return response()->json([
                            'message' => 'Duplicate column'
                        ]);
                    }

                    return response()->json([
                        'message' => 'Product Field Added Successfully',
                        'data' => $saletype,
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Sale id not exist',
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Only admin can fill this form',
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
    public function show(SaleProductList $saleProductList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SaleProductList $saleProductList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SaleProductList $saleProductList)
    {
        //
    }

    public function showProductViasellType(Request $request)
    {
        ## showing the news on the basis of sale type 
        $validator = Validator::make($request->all(), [
            'sale_id' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        $sale_id = $request->sale_id;
        try {
            $salepro = SaleProductList::where('sale_id', $sale_id)->first();
            if (!empty($salepro)) {

                $productFieldList = SaleProductList::where('sale_id', $sale_id)
                    ->select('sale_product_lists.id', 'sale_product_lists.field_name', 'sales.type', 'sale_product_lists.created_at', 'sale_product_lists.updated_at')
                    ->join('sales', 'sales.id', 'sale_product_lists.sale_id')
                    ->get();
                if (!empty($productFieldList)) {
                    return response()->json([
                        'message' => 'All Field List List On The Sale Type Basis',
                        'data' => $productFieldList,
                    ]);
                } else {
                    return response()->json([
                        'error' => 'Data not found',
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'sale type not found',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
