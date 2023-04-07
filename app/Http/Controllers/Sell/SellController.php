<?php

namespace App\Http\Controllers\Sell;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class SellController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sa_id = auth()->user()->id;
        try {
            if ($sa_id == 1) {
                $getsell = Sale::orderBy('id', 'desc')->get();
                if (!empty($getsell)) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Category List',
                        'data' => $getsell,
                    ]);
                } else {
                    return response()->json([
                        'status' => true,
                        'message' => 'No data found',
                        'data' => [],
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Only admin have access',
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
                $validator = Validator::make($request->all(), [
                    'type' => ['required', 'alpha', 'string', 'max:255', 'in:vehicle,property,other'],
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()]);
                }
                $chkselltype = Sale::where('type', $request->type)->first();
                if (empty($chkselltype)) {
                    $selltype = Sale::create([
                        'type' => $request->type,
                    ]);
                    return response()->json([
                        'status' => true,
                        'message' => 'Category Added Successfully',
                        'data' => $selltype,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Category Already Exist',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Only admin can add category',
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
        $sa_id = auth()->user()->id;
        try {
            if ($sa_id == 1) {
                $getsellid = Sale::where('id', $id)->first();
                if (!empty($getsellid)) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Details',
                        'data' => $getsellid,
                    ]);
                } else {
                    return response()->json([
                        'status' => true,
                        'message' => 'Not exist',
                        'data' => [],
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Only admin can add category',
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

                $sell = Sale::where('id', $id)->first();
                if (!empty($sell)) {
                    $validator = Validator::make($request->all(), [
                        'type' => ['required', 'alpha', 'string', 'max:255'],
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['status' => false, 'message' => $validator->errors()]);
                    }
                    $updatesell = $sell->update([
                        'type' => $request->type,
                    ]);
                    return response()->json([
                        'status' => true,
                        'message' => 'Category Updated Successfully',
                        'data' => $sell,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Record Not Exist',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Only admin can update category',
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
        $sa_id = auth()->user()->id;
        try {
            if ($sa_id == 1) {
                $sell = Sale::where('id', $id)->first();
                if (!empty($sell)) {
                    $delsell = $sell->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'Category Deleted Successfully',
                        'data' => $sell,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Type Not Exist',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Only admin can add category',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
