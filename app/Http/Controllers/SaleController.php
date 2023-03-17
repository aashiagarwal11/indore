<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $getsale = Sale::all();
            if (!empty($getsale)) {
                return response()->json([
                    'message' => 'All Sale Type List',
                    'data' => $getsale,
                ]);
            } else {
                return response()->json([
                    'message' => 'No Sale Type Found In The List',
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
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'alpha', 'string', 'max:255','in:vehicle,property,other'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        try {
            if ($sa_id == 1) {
                $chksaletype = Sale::where('type', $request->type)->first();
                if (empty($chksaletype)) {
                    $saletype = Sale::create([
                        'type' => $request->type,
                    ]);
                    return response()->json([
                        'message' => 'Sale Type Added Successfully',
                        'data' => $saletype,
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Sale Type Already Exist',
                    ]);
                }
            }else {
                return response()->json([
                    'message' => 'Only admin can add sale type',
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
            $getsaleid = Sale::where('id', $id)->first();
            if (!empty($getsaleid)) {
                return response()->json([
                    'message' => 'Details',
                    'data' => $getsaleid,
                ]);
            } else {
                return response()->json([
                    'message' => 'Not exist',
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
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'alpha', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        try {
            if ($sa_id == 1) {
                $sale = Sale::where('id', $id)->first();
                if (!empty($sale)) {
                    $updatesale = $sale->update([
                        'type' => $request->type,
                    ]);
                    return response()->json([
                        'message' => 'Sale Type Updated Successfully',
                        'data' => $sale,
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Record Not Exist',
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Only admin can add sale type',
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
            $sale = Sale::where('id', $id)->first();
            if (!empty($sale)) {
                $delsale = $sale->delete();
                return response()->json([
                    'message' => 'Sale Type Deleted Successfully',
                    'data' => $sale,
                ]);
            } else {
                return response()->json([
                    'message' => 'Type Not Exist',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
