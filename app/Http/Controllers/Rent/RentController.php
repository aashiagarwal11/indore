<?php

namespace App\Http\Controllers\Rent;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Rent;
use App\Models\City;

class RentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        # Accepted list of rent product
        try {
            $rentingproduct = Rent::where('status', 1)->orderBy('id', 'desc')->get()->toArray();
            if (!empty($rentingproduct)) {
                $newarr = [];
                foreach ($rentingproduct as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    array_push($newarr, $new);
                }
                return response()->json([
                    'message' => 'List of product want to give on rent',
                    'data' => $newarr,
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
        # rent request by user
        $auth_id = auth()->user()->id;
        $role_id = auth()->user()->role_id;
        try {
            if (!empty($auth_id)) {
                if ($role_id == 2) {
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
                            'sub_cat_id'        => ['required', 'numeric'],
                            'vendor_name'       => ['required', 'string', 'max:50'],
                            'owner_or_broker'   => ['required', 'string', 'max:255', 'in:owner,broker'],
                            'property_location' => ['required', 'string', 'max:255'],
                            'price'             => ['required'],
                            'image'             => ['required'],
                            'image.*'           => ['mimes:jpeg,png,jpg,svg'],
                            'whatsapp_no'       => ['required', 'numeric', 'digits:10'],
                            'call_no'           => ['required'],
                        ]);
                        if ($validator->fails()) {
                            return response()->json(['message' => $validator->errors()]);
                        }

                        if (!empty($chksellid->type)) {
                            $images = array();
                            if ($files = $request->file('image')) {
                                foreach ($files as $file) {
                                    $imgname = md5(rand('1000', '10000'));
                                    $extension = strtolower($file->getClientOriginalExtension());
                                    $img_full_name = $imgname . '.' . $extension;
                                    $upload_path = 'public/rentimage/';
                                    $img_url = $upload_path . $img_full_name;
                                    $file->move($upload_path, $img_full_name);
                                    array_push($images, $img_url);
                                }
                            }

                            $imp_image =  implode('|', $images);

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
                                    'color'             => ['required', 'string', 'max:20'],
                                ]);
                                if ($validator->fails()) {
                                    return response()->json(['message' => $validator->errors()]);
                                }
                                $rent = Rent::create([
                                    'sale_id'           => $sell_id,
                                    'sub_cat_id'        => $request->sub_cat_id,
                                    'vendor_name'       => $request->vendor_name,
                                    'owner_or_broker'   => $request->owner_or_broker,
                                    'property_location' => $request->property_location,
                                    'price'             => $request->price,
                                    'vehicle_sighting'  => $request->vehicle_sighting,
                                    'brand'             => $request->brand,
                                    'model_name'        => $request->model_name,
                                    'model_year'        => $request->model_year,
                                    'fuel_type'         => $request->fuel_type,
                                    'seater'            => $request->seater,
                                    'kilometer_running' => $request->kilometer_running,
                                    'insurance_period'  => $request->insurance_period,
                                    'color'             => $request->color,
                                    'other_information' => $request->other_information ?? null,
                                    'image'             => $imp_image,
                                    'city_id'           => $request->city_id ?? null,
                                    'user_id'           => $auth_id,
                                    'whatsapp_no'       => $request->whatsapp_no,
                                    'call_no'           => $request->call_no,
                                ]);

                                $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                                $exp = explode('|',  $imp_image);
                                $rent['image'] = $exp;

                                return response()->json([
                                    'message' => 'Rent Product added successfully for vehicle',
                                    'data' => $rent,
                                ]);
                            } elseif ($chksellid->type == 'property') {
                                $validator = Validator::make($request->all(), [
                                    'size_length_width'  => ['required', 'number'],
                                    'room_qty'           => ['required', 'number'],
                                    'kitchen'            => ['required', 'number'],
                                    'hall'               => ['required', 'number'],
                                    'lat_bath'           => ['required', 'string', 'in:attach,non-attach'],
                                ]);
                                if ($validator->fails()) {
                                    return response()->json(['message' => $validator->errors()]);
                                }
                                $rent = Rent::create([
                                    'sale_id'           => $sell_id,
                                    'sub_cat_id'        => $request->sub_cat_id,
                                    'vendor_name'       => $request->vendor_name,
                                    'owner_or_broker'   => $request->owner_or_broker,
                                    'property_location' => $request->property_location,
                                    'price'             => $request->price,
                                    'size_length_width' => $request->size_length_width,
                                    'other_information' => $request->other_information ?? null,
                                    'image'             => $imp_image,
                                    'city_id'           => $request->city_id ?? null,
                                    'user_id'           => $auth_id,
                                    'whatsapp_no'       => $request->whatsapp_no,
                                    'call_no'           => $request->call_no,
                                    'room_qty'          => $request->room_qty,
                                    'kitchen'           => $request->kitchen,
                                    'hall'              => $request->hall,
                                    'lat_bath'          => $request->lat_bath,
                                ]);

                                $exp = explode('|',  $imp_image);
                                $rent['image'] = $exp;
                                return response()->json([
                                    'message' => 'Rent Product added successfully for property',
                                    'data' => $rent,
                                ]);
                            }
                        } else {
                            return response()->json([
                                'message' => 'Rent type not exist',
                            ]);
                        }
                    } else {
                        return response()->json([
                            'message' => 'Rent id not exist',
                        ]);
                    }
                } else {
                    return response()->json([
                        'message' => 'Login as user',
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
    public function show(string $id)
    {
        ## show specificnews by id
        try {
            $showRentid = Rent::where('rents.id', $id)
                ->select('rents.*', 'users.name', 'cities.city_name')
                ->join('users', 'users.id', 'rents.user_id')
                ->join('cities', 'cities.id', 'rents.city_id')
                ->get();
            if (!empty($showRentid)) {
                $newarr = [];
                foreach ($showRentid as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    array_push($newarr, $new);
                }
                return response()->json([
                    'message' => 'Show the result of rent product with particular Id',
                    'data' => $newarr,
                ]);
            } else {
                return response()->json([
                    'message' => 'No Record Exist',
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
        #edit user request by admin
        $auth_id = auth()->user()->id;
        $role_id = auth()->user()->role_id;
        try {
            if (!empty($auth_id)) {
                if ($role_id == 1) {
                    $rentpro = Rent::where('id', $id)->first();
                    if (!empty($rentpro)) {
                        $chksellid = Sale::where('id', $rentpro->sale_id)->first();

                        if (!empty($chksellid)) {
                            $validator = Validator::make($request->all(), [
                                'city_id' => ['required', 'numeric'],
                            ]);

                            if ($validator->fails()) {
                                return response()->json(['message' => $validator->errors()]);
                            }

                            $city = City::where('id', $request->city_id)->first();

                            if (!empty($city)) {
                                $validator = Validator::make($request->all(), [
                                    'sale_id'           => ['required', 'numeric'],
                                    'sub_cat_id'        => ['required', 'numeric'],
                                    'vendor_name'       => ['required', 'string', 'max:50'],
                                    'owner_or_broker'   => ['required', 'string', 'max:255', 'in:owner,broker'],
                                    'property_location' => ['required', 'string', 'max:255'],
                                    'price'             => ['required'],
                                    'image'             => ['required'],
                                    'image.*'           => ['mimes:jpeg,png,jpg,svg'],
                                    'whatsapp_no'       => ['required', 'numeric', 'digits:10'],
                                    'call_no'           => ['required'],
                                ]);
                                if ($validator->fails()) {
                                    return response()->json(['message' => $validator->errors()]);
                                }

                                if (!empty($chksellid->type)) {
                                    $images = array();
                                    if ($files = $request->file('image')) {
                                        foreach ($files as $file) {
                                            $imgname = md5(rand('1000', '10000'));
                                            $extension = strtolower($file->getClientOriginalExtension());
                                            $img_full_name = $imgname . '.' . $extension;
                                            $upload_path = 'public/rentimage/';
                                            $img_url = $upload_path . $img_full_name;
                                            $file->move($upload_path, $img_full_name);
                                            array_push($images, $img_url);
                                        }
                                    }

                                    $imp_image =  implode('|', $images);

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
                                            'color'             => ['required', 'string', 'max:20'],
                                        ]);
                                        if ($validator->fails()) {
                                            return response()->json(['message' => $validator->errors()]);
                                        }

                                        $data['sale_id']           = $request->sale_id;
                                        $data['sub_cat_id']        = $request->sub_cat_id;
                                        $data['vendor_name']       = $request->vendor_name;
                                        $data['owner_or_broker']   = $request->owner_or_broker;
                                        $data['property_location'] = $request->property_location;
                                        $data['price']             = $request->price;
                                        $data['whatsapp_no']       = $request->whatsapp_no;
                                        $data['call_no']           = $request->call_no;
                                        $data['vehicle_sighting']  = $request->vehicle_sighting;
                                        $data['brand']             = $request->brand;
                                        $data['model_name']        = $request->model_name;
                                        $data['model_year']        = $request->model_year;
                                        $data['fuel_type']         = $request->fuel_type;
                                        $data['seater']            = $request->seater;
                                        $data['kilometer_running'] = $request->kilometer_running;
                                        $data['insurance_period']  = $request->insurance_period;
                                        $data['color']             = $request->color;
                                        $data['other_information'] = $request->other_information ?? null;
                                        $data['image']             = $imp_image;
                                        $data['city_id']           = $request->city_id;
                                        // $data['user_id']           = $auth_id;

                                        $updatedata =  $rentpro->update($data);
                                        $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                                        $exp = explode('|',  $imp_image);
                                        $data['image'] = $exp;

                                        return response()->json([
                                            'message' => 'Product updated successfully',
                                            'data' => $data,
                                        ]);
                                    } elseif ($chksellid->type == 'property') {
                                        $validator = Validator::make($request->all(), [
                                            'size_length_width'  => ['required', 'number'],
                                            'room_qty'           => ['required', 'number'],
                                            'kitchen'            => ['required', 'number'],
                                            'hall'               => ['required', 'number'],
                                            'lat_bath'           => ['required', 'string', 'in:attach,non-attach'],
                                        ]);
                                        if ($validator->fails()) {
                                            return response()->json(['message' => $validator->errors()]);
                                        }
                                        $data['sale_id']           = $request->sale_id;
                                        $data['sub_cat_id']        = $request->sub_cat_id;
                                        $data['vendor_name']       = $request->vendor_name;
                                        $data['owner_or_broker']   = $request->owner_or_broker;
                                        $data['property_location'] = $request->property_location;
                                        $data['price']             = $request->price;
                                        $data['size_length_width'] = $request->size_length_width;
                                        $data['other_information'] = $request->other_information ?? null;
                                        $data['image']             = $imp_image;
                                        $data['city_id']           = $request->city_id;
                                        // $data['user_id']           = $auth_id;
                                        $data['whatsapp_no']       = $request->whatsapp_no;
                                        $data['call_no']           = $request->call_no;
                                        $data['room_qty']          = $request->room_qty;
                                        $data['kitchen']           = $request->kitchen;
                                        $data['hall']              = $request->hall;
                                        $data['lat_bath']          = $request->lat_bath;

                                        $updatedata =  $rentpro->update($data);

                                        $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                                        $exp = explode('|',  $imp_image);
                                        $data['image'] = $exp;

                                        return response()->json([
                                            'message' => 'Product updated successfully',
                                            'data' => $data,
                                        ]);
                                    }
                                } else {
                                    return response()->json([
                                        'message' => 'No sell category exist',
                                    ]);
                                }
                            } else {
                                return response()->json([
                                    'error' => 'City not exist',
                                ]);
                            }
                        } else {
                            return response()->json([
                                'message' => 'Sell id not exist',
                            ]);
                        }
                    } else {
                        return response()->json([
                            'message' => 'Record not exist',
                        ]);
                    }
                } else {
                    return response()->json([
                        'message' => 'Login as admin first',
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Sorry!! Only admin can edit details, login as admin first',
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
    public function destroy(string $id)
    {
        try {
            $delete = Rent::where('id', $id)->first();
            if (!empty($delete)) {
                $getdeleterec = $delete->delete();
                return response()->json([
                    'message' => 'Record Deleted Successfully',
                ]);
            } else {
                return response()->json([
                    'message' => 'Record Not Exist',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function addRentProductViaAdmin(Request $request)
    {
        # rent request by admin
        $auth_id = auth()->user()->id;
        $role_id = auth()->user()->role_id;

        try {
            if (!empty($auth_id)) {
                if ($role_id == 1) {
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
                            'city_id' => ['required', 'numeric'],
                        ]);

                        if ($validator->fails()) {
                            return response()->json(['message' => $validator->errors()]);
                        }

                        $city = City::where('id', $request->city_id)->first();

                        if (!empty($city)) {
                            $validator = Validator::make($request->all(), [
                                'sub_cat_id'       => ['required', 'numeric'],
                                'vendor_name'       => ['required', 'string', 'max:50'],
                                'owner_or_broker'   => ['required', 'string', 'max:255', 'in:owner,broker'],
                                'property_location' => ['required', 'string', 'max:255'],
                                'price'             => ['required'],
                                'image'             => ['required'],
                                'image.*'           => ['mimes:jpeg,png,jpg,svg'],
                                'whatsapp_no'       => ['required', 'numeric', 'digits:10'],
                                'call_no'           => ['required'],
                            ]);
                            if ($validator->fails()) {
                                return response()->json(['message' => $validator->errors()]);
                            }

                            if (!empty($chksellid->type)) {
                                $images = array();
                                if ($files = $request->file('image')) {
                                    foreach ($files as $file) {
                                        $imgname = md5(rand('1000', '10000'));
                                        $extension = strtolower($file->getClientOriginalExtension());
                                        $img_full_name = $imgname . '.' . $extension;
                                        $upload_path = 'public/rentimage/';
                                        $img_url = $upload_path . $img_full_name;
                                        $file->move($upload_path, $img_full_name);
                                        array_push($images, $img_url);
                                    }
                                }

                                $imp_image =  implode('|', $images);

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
                                        'color'             => ['required', 'string', 'max:20'],
                                    ]);
                                    if ($validator->fails()) {
                                        return response()->json(['message' => $validator->errors()]);
                                    }
                                    $rent = Rent::create([
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
                                        'image' => $imp_image,
                                        'city_id' => $request->city_id,
                                        'user_id' => $auth_id,
                                        'whatsapp_no'       => $request->whatsapp_no,
                                        'call_no'           => $request->call_no,
                                    ]);

                                    $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                                    $exp = explode('|',  $imp_image);
                                    $rent['image'] = $exp;

                                    return response()->json([
                                        'message' => 'Rent product added successfully for vehicle',
                                        'data' => $rent,
                                    ]);
                                } elseif ($chksellid->type == 'property') {
                                    $validator = Validator::make($request->all(), [
                                        'size_length_width'  => ['required', 'number'],
                                        'room_qty'           => ['required', 'number'],
                                        'kitchen'            => ['required', 'number'],
                                        'hall'               => ['required', 'number'],
                                        'lat_bath'           => ['required', 'string', 'in:attach,non-attach'],
                                    ]);
                                    if ($validator->fails()) {
                                        return response()->json(['message' => $validator->errors()]);
                                    }
                                    $rent = Rent::create([
                                        'sale_id'           => $sell_id,
                                        'sub_cat_id'        => $request->sub_cat_id,
                                        'vendor_name'       => $request->vendor_name,
                                        'owner_or_broker'   => $request->owner_or_broker,
                                        'property_location' => $request->property_location,
                                        'price'             => $request->price,
                                        'size_length_width' => $request->size_length_width,
                                        'other_information' => $request->other_information ?? null,
                                        'image'             => $imp_image,
                                        'city_id'           => $request->city_id,
                                        'user_id'           => $auth_id,
                                        'whatsapp_no'       => $request->whatsapp_no,
                                        'call_no'           => $request->call_no,
                                        'room_qty'          => $request->room_qty,
                                        'kitchen'           => $request->kitchen,
                                        'hall'              => $request->hall,
                                        'lat_bath'          => $request->lat_bath,
                                    ]);

                                    $exp = explode('|',  $imp_image);
                                    $rent['image'] = $exp;
                                    return response()->json([
                                        'message' => 'Rent product added successfully for property',
                                        'data' => $rent,
                                    ]);
                                }
                            } else {
                                return response()->json([
                                    'message' => 'Rent type not exist',
                                ]);
                            }
                        } else {
                            return response()->json([
                                'error' => 'City not exist',
                            ]);
                        }
                    } else {
                        return response()->json([
                            'message' => 'Rent id not exist',
                        ]);
                    }
                } else {
                    return response()->json([
                        'message' => 'Login as admin',
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Sorry!! Only admin can add details, login as admin first',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function rentFormListOfUser(Request $request)
    {
        ## all list of renting product form from user side we show this list on admin panel so that admin can accept and deny the rent product.
        try {
            $rentingproduct = Rent::orderBy('id', 'desc')->get()->toArray();
            if (!empty($rentingproduct)) {
                $newarr = [];
                foreach ($rentingproduct as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    array_push($newarr, $new);
                }
                return response()->json([
                    'message' => 'List of product want to give on rent',
                    'data' => $newarr,
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

    public function acceptRentProduct(Request $request)
    {
        ## request accept  by the admin for user rent product form 
        $auth_id = auth()->user()->id;
        $id = $request->id;
        try {
            $validator = Validator::make($request->all(), [
                'id' => ['required', 'numeric'],
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()]);
            }
            $rent = Rent::where('id', $id)->first();
            if (!empty($rent)) {
                $rentcity = Rent::where('id', $id)->where('city_id', '!=', null)->first();
                if (!empty($rentcity)) {
                    if ($rent->status == 0) {
                        $rent->status = 1;
                        $updateStatus = $rent->update();
                        return response()->json([
                            'message' => 'Rent product request is accepted By Admin',
                            'data' => $rent,
                        ]);
                    } else {
                        if ($rent->status == 2) {
                            return response()->json([
                                'message' => 'Renting product request already denied By Admin so you can not accept',
                            ]);
                        } else {
                            return response()->json([
                                'message' => 'Request is already accepted By Admin',
                            ]);
                        }
                    }
                } else {
                    return response()->json([
                        'message' => 'Please add city first',
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Record Not Exist',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function denyRentProduct(Request $request)
    {
        ## request deny  by the admin for user rent product form 
        $auth_id = auth()->user()->id;
        $id = $request->id;
        try {
            $validator = Validator::make($request->all(), [
                'id' => ['required', 'numeric'],
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()]);
            }
            $rent = Rent::where('id', $id)->first();
            if (!empty($rent)) {
                if ($rent->status == 0) {
                    $rent->status = 2;
                    $updateStatus = $rent->update();
                    return response()->json([
                        'message' => 'Renting product request denied By Admin',
                        'data' => $rent,
                    ]);
                } else {
                    if ($rent->status == 1) {
                        return response()->json([
                            'message' => 'Renting product request already accepted By Admin so you can not deny',
                        ]);
                    } else {
                        return response()->json([
                            'message' => 'Renting product request is already denied By Admin',
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'message' => 'Record Not Exist',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function showRentProductViacity(Request $request)
    {
        ## showing the renting product list on the city basis

        $city_id = $request->city_id;
        try {
            if ($city_id == null) {
                $rentproduct = Rent::where('status', 1)->orderBy('id', 'desc')->get()->toArray();
                $newarr = [];
                foreach ($rentproduct as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    array_push($newarr, $new);
                }
                return response()->json([
                    'message' => 'All Renting product list',
                    'data' => $newarr,
                ]);
            } else {
                $city = City::where('id', $city_id)->first();
                if (!empty($city)) {
                    $rentproduct = Rent::where('rents.city_id', $city_id)->where('status', 1)
                        ->select('rents.*', 'users.name', 'cities.city_name')
                        ->join('users', 'users.id', 'rents.user_id')
                        ->join('cities', 'cities.id', 'rents.city_id')
                        ->orderBy('rents.id', 'desc')
                        ->get();
                    if (!empty($rentproduct)) {
                        $newarr = [];
                        foreach ($rentproduct as $key => $new) {
                            $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                            $new['image'] = explode('|', $new['image']);
                            array_push($newarr, $new);
                        }
                        return response()->json([
                            'message' => 'Renting product list on the city basis',
                            'data' => $rentproduct,
                        ]);
                    } else {
                        return response()->json([
                            'error' => 'No News Found',
                        ]);
                    }
                } else {
                    return response()->json([
                        'message' => 'City Not Exist',
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
