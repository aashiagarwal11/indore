<?php

namespace App\Http\Controllers\Sell;

use App\Http\Controllers\Controller;
use App\Models\SaleSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Sale;


class SellSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $getsalesubcat = SaleSubCategory::join('sales', 'sales.id', 'sale_sub_categories.sale_id')
                ->select('sale_sub_categories.id', 'sales.type', 'sale_sub_categories.sub_type', 'sale_sub_categories.created_at', 'sale_sub_categories.updated_at')->orderBy('sale_sub_categories.id', 'desc')->get();

            if (!empty($getsalesubcat)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Sub Category List',
                    'data' => $getsalesubcat,
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'No sub categories found',
                    'data' => [],
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
        $sa_id = auth()->user()->id;
        try {
            if ($sa_id == 1) {
                $chksale = Sale::where('id', $request->sale_id)->first();
                if (!empty($chksale)) {
                    $validator = Validator::make($request->all(), [
                        'sale_id' => ['required', 'numeric'],
                        'sub_type' => ['required', 'alpha', 'string', 'max:255'],
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['status' => false, 'message' => $validator->errors()]);
                    }
                    $chk_sale_id = SaleSubCategory::where('sub_type', $request->sub_type)->first();
                    if (empty($chk_sale_id)) {
                        $sale_sub_type = SaleSubCategory::create([
                            'sale_id' => $request->sale_id,
                            'sub_type' => $request->sub_type,
                        ]);
                        return response()->json([
                            'status' => true,
                            'message' => 'Sub Category Added Successfully',
                            'data' => $sale_sub_type,
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Sub Category Already Exist',
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Category not exist',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Only admin can add sub category',
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
    public function show($id)
    {
        try {
            $getsaleid = SaleSubCategory::where('id', $id)->first();
            if (!empty($getsaleid)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Details',
                    'data' => $getsaleid,
                ]);
            } else {
                return response()->json([
                    'status' => false,
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $sa_id = auth()->user()->id;
        try {
            if ($sa_id == 1) {
                $chksale = Sale::where('id', $request->sale_id)->first();
                if (!empty($chksale)) {
                    $validator = Validator::make($request->all(), [
                        'sale_id' => ['required', 'numeric'],
                        'sub_type' => ['required', 'alpha', 'string', 'max:255'],
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['status' => false, 'message' => $validator->errors()]);
                    }
                    $chk_id = SaleSubCategory::where('id', $id)->first();
                    if (!empty($chk_id)) {
                        $updatesale = $chk_id->update([
                            'sale_id' => $request->sale_id,
                            'sub_type' => $request->sub_type,
                        ]);
                        return response()->json([
                            'status' => true,
                            'message' => 'Sub Category Updated Successfully',
                            'data' => $chk_id,
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Record not exist',
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Category not exist',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Only admin can add sub category',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $saleSubCategory = SaleSubCategory::where('id', $id)->first();
            if (!empty($saleSubCategory)) {
                $delsaleSubCategory = $saleSubCategory->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Sub Category Deleted Successfully',
                    'data' => $saleSubCategory,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Record not exist',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function showSellSubCategoryViaSelltype(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sale_id' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()]);
        }
        $sale_id = $request->sale_id;
        try {
            $city = SaleSubCategory::where('id', $sale_id)->first();
            if (!empty($city)) {
                // $news = News::where('city_id', $city_id)->where('status', 1)->get();

                $salesubcat = SaleSubCategory::where('sale_id', $sale_id)
                    ->select('sales.type', 'sale_sub_categories.id', 'sale_sub_categories.sub_type', 'sale_sub_categories.created_at', 'sale_sub_categories.updated_at')
                    ->join('sales', 'sales.id', 'sale_sub_categories.sale_id')
                    ->orderBy('sale_sub_categories.id', 'desc')
                    ->get();
                if (!empty($salesubcat)) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Sub category list basis on sale type',
                        'data' => $salesubcat,
                    ]);
                } else {
                    return response()->json([
                        'status' => true,
                        'message' => 'No sub category found',
                        'data' => [],
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Sell Type Not Exist',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
