<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\City;


class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $getcity = City::all();
            if (!empty($getcity)) {
                return response()->json([
                    'message' => 'All City Details',
                    'data' => $getcity,
                ]);
            } else {
                return response()->json([
                    'message' => 'No City Found In The List',
                ]);
            }
        } catch (\Exception $e) {
            response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_name' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        try {
            $chkcityname = City::where('city_name', $request->city_name)->first();
            if (empty($chkcityname)) {
                $city = City::create([
                    'city_name' => $request->city_name,
                ]);
                return response()->json([
                    'message' => 'City Added Successfully',
                    'data' => $city,
                ]);
            } else {
                return response()->json([
                    'message' => 'City Already Exist',
                ]);
            }
        } catch (\Exception $e) {
            response()->json([
                'message' => $e->getMessage(),
            ]);
            // return ApiResponse::error($e->getMessage());
            // logger($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'city_name' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        try {
            $city = City::where('id', $id)->first();
            if (!empty($city)) {
                $updatecity = $city->update([
                    'city_name' => $request->city_name,
                ]);
                return response()->json([
                    'message' => 'City Updated Successfully',
                    'data' => $city,
                ]);
            } else {
                return response()->json([
                    'message' => 'Record Not Exist',
                ]);
            }
        } catch (\Exception $e) {
            response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $city = City::where('id', $id)->first();
            if (!empty($city)) {
                $delcity = $city->delete();
                return response()->json([
                    'message' => 'City Deleted Successfully',
                    'data' => $city,
                ]);
            } else {
                return response()->json([
                    'message' => 'City Not Exist',
                ]);
            }
        } catch (\Exception $e) {
            response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
