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
        try {
            $getsell = Sale::all();
            if (!empty($getsell)) {
                return response()->json([
                    'message' => 'All Sell Type List',
                    'data' => $getsell,
                ]);
            } else {
                return response()->json([
                    'message' => 'No Sell Type Found In The List',
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
                $chkselltype = Sale::where('type', $request->type)->first();
                if (empty($chkselltype)) {
                    $selltype = Sale::create([
                        'type' => $request->type,
                    ]);
                    return response()->json([
                        'message' => 'Sell Type Added Successfully',
                        'data' => $selltype,
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Sell Type Already Exist',
                    ]);
                }
            }else {
                return response()->json([
                    'message' => 'Only admin can add sell type',
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
            $getsellid = Sale::where('id', $id)->first();
            if (!empty($getsellid)) {
                return response()->json([
                    'message' => 'Details',
                    'data' => $getsellid,
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
                $sell = Sale::where('id', $id)->first();
                if (!empty($sell)) {
                    $updatesell = $sell->update([
                        'type' => $request->type,
                    ]);
                    return response()->json([
                        'message' => 'Sell Type Updated Successfully',
                        'data' => $sell,
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Record Not Exist',
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Only admin can add sell type',
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
            $sell = Sale::where('id', $id)->first();
            if (!empty($sell)) {
                $delsell = $sell->delete();
                return response()->json([
                    'message' => 'Sell Type Deleted Successfully',
                    'data' => $sell,
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
