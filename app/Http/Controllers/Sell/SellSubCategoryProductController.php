<?php

namespace App\Http\Controllers\Sell;

use App\Http\Controllers\Controller;
use App\Models\SaleProductList;
use App\Models\SaleSubCategory;
use App\Models\SaleSubCategoryProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Sale;
use Illuminate\Support\Facades\Schema;
use App\Models\City;
use App\Models\Advertisment;

class SellSubCategoryProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ## all list of selling product form from user side which is accepted by admin we show this list on user panel.
        try {
            $sellingproduct = SaleSubCategoryProduct::where('status', 1)->orderBy('id', 'desc')->get()->toArray();
            if (!empty($sellingproduct)) {
                $newarr = [];
                foreach ($sellingproduct as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    array_push($newarr, $new);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'List of product want to sell',
                    'data' => $newarr,
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'No data exist',
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
        $auth_id = auth()->user()->id;
        $role_id = auth()->user()->role_id;


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
            if (!empty($auth_id)) {
                if ($role_id == 2) {
                    $validator = Validator::make($request->all(), [
                        'sale_id' => ['required', 'numeric'],
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['status' => false, 'message' => $validator->errors()]);
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
                            'image.*'           => ['mimes:jpeg,png,jpg'],
                            'whatsapp_no'       => ['required', 'numeric', 'digits:10'],
                            'call_no'           => ['required'],
                        ]);
                        if ($validator->fails()) {
                            return response()->json(['status' => false, 'message' => $validator->errors()]);
                        }

                        if (!empty($chksellid->type)) {
                            $images = array();
                            if ($files = $request->file('image')) {
                                foreach ($files as $file) {
                                    $imgname = md5(rand('1000', '10000'));
                                    $extension = strtolower($file->getClientOriginalExtension());
                                    $img_full_name = $imgname . '.' . $extension;
                                    $upload_path = 'public/sellImage/';
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
                                    return response()->json(['status' => false, 'message' => $validator->errors()]);
                                }
                                $sellproduct = SaleSubCategoryProduct::create([
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
                                $sellproduct['image'] = $exp;

                                return response()->json([
                                    'status' => true,
                                    'message' => 'Product added successfully',
                                    'data' => $sellproduct,
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
                                    return response()->json(['status' => false, 'message' => $validator->errors()]);
                                }
                                $sellproduct = SaleSubCategoryProduct::create([
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
                                $sellproduct['image'] = $exp;
                                return response()->json([
                                    'status' => true,
                                    'message' => 'Product for property added successfully',
                                    'data' => $sellproduct,
                                ]);
                            }
                        } else {
                            return response()->json([
                                'status' => false,
                                'message' => 'No sell type exist',
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Sell id not exist',
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Login as user',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
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
    public function show($id)
    {
        ## show specificnews by id
        try {
            $specificnews = SaleSubCategoryProduct::where('sale_sub_category_products.id', $id)
                ->select('sale_sub_category_products.*', 'users.name', 'cities.city_name')
                ->join('users', 'users.id', 'sale_sub_category_products.user_id')
                ->join('cities', 'cities.id', 'sale_sub_category_products.city_id')
                ->get();
            if (!empty($specificnews)) {
                $newarr = [];
                foreach ($specificnews as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    array_push($newarr, $new);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Selling Product with particular Id',
                    'data' => $newarr,
                ]);
            } else {
                return response()->json([
                    'status' => false,
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
        $auth_id = auth()->user()->id;
        $role_id = auth()->user()->role_id;
        try {
            if (!empty($auth_id)) {
                if ($role_id == 1) {
                    $sellpro = SaleSubCategoryProduct::where('id', $id)->first();
                    if (!empty($sellpro)) {
                        $chksellid = Sale::where('id', $sellpro->sale_id)->first();

                        if (!empty($chksellid)) {
                            $validator = Validator::make($request->all(), [
                                'city_id' => ['required', 'numeric'],
                            ]);

                            if ($validator->fails()) {
                                return response()->json(['status' => false, 'message' => $validator->errors()]);
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
                                    'image.*'           => ['mimes:jpeg,png,jpg'],
                                    'whatsapp_no'       => ['required', 'numeric', 'digits:10'],
                                    'call_no'           => ['required'],
                                ]);
                                if ($validator->fails()) {
                                    return response()->json(['status' => false, 'message' => $validator->errors()]);
                                }

                                if (!empty($chksellid->type)) {
                                    $images = array();
                                    if ($files = $request->file('image')) {
                                        foreach ($files as $file) {
                                            $imgname = md5(rand('1000', '10000'));
                                            $extension = strtolower($file->getClientOriginalExtension());
                                            $img_full_name = $imgname . '.' . $extension;
                                            $upload_path = 'public/sellImage/';
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
                                            return response()->json(['status' => false, 'message' => $validator->errors()]);
                                        }

                                        // $data['sale_id']           = $request->sale_id;
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

                                        $updatedata =  $sellpro->update($data);
                                        $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                                        $exp = explode('|',  $imp_image);
                                        $data['image'] = $exp;

                                        return response()->json([
                                            'status' => true,
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
                                            return response()->json(['status' => false, 'message' => $validator->errors()]);
                                        }
                                        // $data['sale_id']           = $request->sale_id;
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

                                        $updatedata =  $sellpro->update($data);

                                        $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                                        $exp = explode('|',  $imp_image);
                                        $data['image'] = $exp;

                                        return response()->json([
                                            'status' => true,
                                            'message' => 'Product updated successfully',
                                            'data' => $data,
                                        ]);
                                    }
                                } else {
                                    return response()->json([
                                        'status' => false,
                                        'message' => 'No category exist',
                                    ]);
                                }
                            } else {
                                return response()->json([
                                    'status' => false,
                                    'message' => 'City not exist',
                                ]);
                            }
                        } else {
                            return response()->json([
                                'status' => false,
                                'message' => 'Sell id not exist',
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Record not exist',
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Login as admin first',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
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
    public function destroy($id)
    {
        try {
            $delete = SaleSubCategoryProduct::where('id', $id)->first();
            if (!empty($delete)) {
                $getdeleterec = $delete->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Record Deleted Successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Record Not Exist',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function sellFormListOfUser(Request $request)
    {
        ## all list of selling product form from user side we show this list on admin panel so that admin can accept and deny the product.
        try {
            $sellingproduct = SaleSubCategoryProduct::orderBy('id', 'desc')->get()->toArray();
            if (!empty($sellingproduct)) {
                $newarr = [];
                foreach ($sellingproduct as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    array_push($newarr, $new);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'List of product want to sell',
                    'data' => $newarr,
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'No data exist',
                    'data' => [],
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function acceptSellProduct(Request $request)
    {
        ## request accept  by the admin for user sell product form 
        $auth_id = auth()->user()->id;

        $id = $request->id;
        try {
            $validator = Validator::make($request->all(), [
                'id' => ['required', 'numeric'],
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()]);
            }
            $eachnews = SaleSubCategoryProduct::where('id', $id)->first();
            if (!empty($eachnews)) {
                $eachnews = SaleSubCategoryProduct::where('id', $id)->where('city_id', '!=', null)->first();
                if (!empty($rentcity)) {
                    if ($eachnews->status == 0) {
                        $eachnews->status = 1;
                        $updateStatus = $eachnews->update();
                        return response()->json([
                            'status' => true,
                            'message' => 'Selling product request accepted By Admin',
                            'data' => $eachnews,
                        ]);
                    } elseif ($eachnews->status == 2) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Selling product request already denied By Admin so you can not accept',
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Please add city first',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Record Not Exist',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function denySellProduct(Request $request)
    {
        ## request deny  by the admin for user sell product form 
        $auth_id = auth()->user()->id;

        $id = $request->id;
        try {
            $validator = Validator::make($request->all(), [
                'id' => ['required', 'numeric'],
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()]);
            }
            $eachnews = SaleSubCategoryProduct::where('id', $id)->first();
            if (!empty($eachnews)) {
                if ($eachnews->status == 0) {
                    $eachnews->status = 2;
                    $updateStatus = $eachnews->update();
                    return response()->json([
                        'status' => true,
                        'message' => 'Selling product request denied By Admin',
                        'data' => $eachnews,
                    ]);
                } elseif ($eachnews->status == 1) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Renting product request already accepted By Admin so you can not deny',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Record Not Exist',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function addSellProductViaAdmin(Request $request)
    {
        $auth_id = auth()->user()->id;
        $role_id = auth()->user()->role_id;

        try {
            if (!empty($auth_id)) {
                if ($role_id == 1) {
                    $validator = Validator::make($request->all(), [
                        'sale_id' => ['required', 'numeric'],
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['status' => false, 'message' => $validator->errors()]);
                    }

                    $sell_id = $request->sale_id;
                    $chksellid = Sale::where('id', $sell_id)->first();
                    if (!empty($chksellid)) {
                        $validator = Validator::make($request->all(), [
                            'city_id' => ['required', 'numeric'],
                        ]);

                        if ($validator->fails()) {
                            return response()->json(['status' => false, 'message' => $validator->errors()]);
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
                                'image.*'           => ['mimes:jpeg,png,jpg'],
                                'whatsapp_no'       => ['required', 'numeric', 'digits:10'],
                                'call_no'           => ['required'],
                            ]);
                            if ($validator->fails()) {
                                return response()->json(['status' => false, 'message' => $validator->errors()]);
                            }

                            if (!empty($chksellid->type)) {
                                $images = array();
                                if ($files = $request->file('image')) {
                                    foreach ($files as $file) {
                                        $imgname = md5(rand('1000', '10000'));
                                        $extension = strtolower($file->getClientOriginalExtension());
                                        $img_full_name = $imgname . '.' . $extension;
                                        $upload_path = 'public/sellImage/';
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
                                        return response()->json(['status' => false, 'message' => $validator->errors()]);
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
                                        'image' => $imp_image,
                                        'city_id' => $request->city_id,
                                        'user_id' => $auth_id,
                                        'whatsapp_no'       => $request->whatsapp_no,
                                        'call_no'           => $request->call_no,
                                    ]);

                                    $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                                    $exp = explode('|',  $imp_image);
                                    $sellproduct['image'] = $exp;

                                    return response()->json([
                                        'status' => true,
                                        'message' => 'Product added successfully in sell list',
                                        'data' => $sellproduct,
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
                                        return response()->json(['status' => false, 'message' => $validator->errors()]);
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
                                        'image' => $imp_image,
                                        'city_id' => $request->city_id,
                                        'user_id' => $auth_id,
                                        'whatsapp_no'       => $request->whatsapp_no,
                                        'call_no'           => $request->call_no,
                                        'room_qty'          => $request->room_qty,
                                        'kitchen'           => $request->kitchen,
                                        'hall'              => $request->hall,
                                        'lat_bath'          => $request->lat_bath,
                                    ]);

                                    $exp = explode('|',  $imp_image);
                                    $sellproduct['image'] = $exp;
                                    return response()->json([
                                        'status' => true,
                                        'message' => 'Product added successfully in sell list',
                                        'data' => $sellproduct,
                                    ]);
                                }
                            } else {
                                return response()->json([
                                    'status' => false,
                                    'message' => 'No sell type exist',
                                ]);
                            }
                        } else {
                            return response()->json([
                                'status' => false,
                                'message' => 'City not exist',
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Sell id not exist',
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Login as admin',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Sorry!! Only admin can add details, login as admin first',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function showSellProductViacity(Request $request)
    {
        ## showing the selling product list on the city basis
        $validator = Validator::make($request->all(), [
            'city_id' => ['numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()]);
        }
        $city_id = $request->city_id;
        try {
            if ($city_id == null) {
                $sellproduct = SaleSubCategoryProduct::where('status', 1)->orderBy('id', 'desc')->get()->toArray();
                $newarr = [];
                foreach ($sellproduct as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);

                    ## random ads
                    $ads = Advertisment::all()->random(1);
                    if (!empty($ads)) {
                        $image = explode('|', $ads[0]->ads_image);
                        shuffle($image);

                        $ads[0]->ads_image = $image[0];
                        $ads[0]->ads_image = str_replace("public", env('APP_URL') . "public", $ads[0]->ads_image);
                    }

                    $new['randomimage'] = $ads[0]->ads_image;
                    array_push($newarr, $new);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'All Selling product list',
                    'data' => $newarr,
                ]);
            } else {
                $city = City::where('id', $city_id)->first();
                if (!empty($city)) {
                    $sellproduct = SaleSubCategoryProduct::where('sale_sub_category_products.city_id', $city_id)->where('status', 1)
                        ->select('sale_sub_category_products.*', 'users.name', 'cities.city_name')
                        ->join('users', 'users.id', 'sale_sub_category_products.user_id')
                        ->join('cities', 'cities.id', 'sale_sub_category_products.city_id')
                        ->orderBy('sale_sub_category_products.id', 'desc')
                        ->get();
                    if (!empty($sellproduct)) {
                        $newarr = [];
                        foreach ($sellproduct as $key => $new) {
                            $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                            $new['image'] = explode('|', $new['image']);

                            ## random ads
                            $ads = Advertisment::all()->random(1);
                            if (!empty($ads)) {
                                $image = explode('|', $ads[0]->ads_image);
                                shuffle($image);

                                $ads[0]->ads_image = $image[0];
                                $ads[0]->ads_image = str_replace("public", env('APP_URL') . "public", $ads[0]->ads_image);
                            }

                            $new['randomimage'] = $ads[0]->ads_image;
                            array_push($newarr, $new);
                        }
                        return response()->json([
                            'status' => true,
                            'message' => 'All Selling product list on the city basis',
                            'data' => $sellproduct,
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'No News Found',
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
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









    // public function acceptDenySell(Request $request)
    // {
    //     dd('acceptdenyproduct');
    //     ## request accept and deny by the admin for user sell form 
    //     $validator = Validator::make($request->all(), [
    //         'status' => ['required', 'numeric', 'in:0,1'],
    //         'id' => ['required'],
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['message' => $validator->errors()]);
    //     }
    //     $getdata =  SaleSubCategoryProduct::all(); ## all data with containing 0 and 1 status
    //     dd($getdata);
    //     $id = $request->id;
    //     try {
    //         if (!empty($getdata)) {
    //             $eachnews = News::where('id', $id)->first();
    //             if (!empty($eachnews)) {
    //                 $updatecity = $eachnews->update([
    //                     'status' => $request->status,
    //                 ]);
    //                 $all = News::where('id', $id)->first();
    //                 if ($request->status == 1) {
    //                     return response()->json([
    //                         'message' => 'Accepted By Admin',
    //                         'data' => $all,
    //                     ]);
    //                 } else {
    //                     return response()->json([
    //                         'message' => 'Deny By Admin',
    //                         'data' => $all,
    //                     ]);
    //                 }
    //             } else {
    //                 return response()->json([
    //                     'message' => 'Record Not Exist',
    //                 ]);
    //             }
    //         } else {
    //             return response()->json([
    //                 'message' => 'No News Available',
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => $e->getMessage(),
    //         ]);
    //     }
    // }
}
