<?php

namespace App\Http\Controllers\Birthday;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Birthday;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\City;
use App\Models\Advertisment;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BirthdayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        # Accepted list of rent product
        try {
            $birthday = Birthday::whereDate('created_at', date('Y-m-d'))->where('status', 1)->orderBy('id', 'desc')->get()->toArray();
            if (!empty($birthday)) {
                $newarr = [];
                foreach ($birthday as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    $new['image'] = ($new['image'][0] != "") ? $new['image'] : [];
                    array_push($newarr, $new);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'List of today birthday',
                    'data' => $newarr,
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Data not found',
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
        ## store news form details by user side
        $id = auth()->user()->id;
        $role_id = auth()->user()->role_id;
        try {
            if (!empty($id)) {
                if ($role_id != 1) {
                    $validator = Validator::make($request->all(), [
                        'title'       => ['required', 'string'],
                        'description' => ['required'],
                        'image'       => ['nullable'],
                        'image.*'     => ['mimes:jpeg,png,jpg'],
                        'video_url'   => ['nullable'],
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['success' => false, 'message' => $validator->errors()]);
                    }

                    $images = array();
                    if ($files = $request->file('image')) {
                        foreach ($files as $file) {
                            $imgname = md5(rand('1000', '10000'));
                            $extension = strtolower($file->getClientOriginalExtension());
                            $img_full_name = $imgname . '.' . $extension;
                            $upload_path = 'public/birthday/';
                            $img_url = $upload_path . $img_full_name;
                            $file->move($upload_path, $img_full_name);
                            array_push($images, $img_url);
                        }
                    }


                    $imp_image =  implode('|', $images);

                    $birthday = Birthday::create([
                        'title' => $request->title,
                        'description' => $request->description,
                        'user_id' => $id,
                        'city_id' => $request->city_id ?? null,
                        'video_url' => $request->video_url ?? null,
                        'image' => $imp_image,
                    ]);
                    $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                    $exp = explode('|',  $imp_image);

                    $birthday['image'] = ($exp[0] != "") ? $exp : [];

                    return response()->json([
                        'success' => true,
                        'message' => 'Birthday Added Successfully',
                        'data' => $birthday,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Login as user',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Login as user',
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        ## add city by admin and edit form of user side
        // $auth_id = auth()->user()->role_id;
        // dd($auth_id);
        try {
            if ($request->role_id == 1) {
                $birthday = Birthday::where('id', $id)->first();
                if (!empty($birthday)) {
                    $validator = Validator::make($request->all(), [
                        'title'       => ['required', 'string'],
                        'description' => ['required'],
                        'city_id'     => ['required', 'numeric'],
                        'image'       => ['nullable'],
                        'image.*'     => ['mimes:jpeg,png,jpg'],
                        'video_url'   => ['nullable'],
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['success' => false, 'message' => $validator->errors()]);
                    }
                    $images = array();
                    if ($files = $request->file('image')) {
                        foreach ($files as $file) {
                            $imgname = md5(rand('1000', '10000'));
                            $extension = strtolower($file->getClientOriginalExtension());
                            $img_full_name = $imgname . '.' . $extension;
                            $upload_path = 'public/birthday/';
                            $img_url = $upload_path . $img_full_name;
                            $file->move($upload_path, $img_full_name);
                            array_push($images, $img_url);
                        }
                    }

                    $imp_image =  implode('|', $images);

                    $data['title'] = $request->title;
                    $data['description'] = $request->description;
                    $data['city_id'] = $request->city_id;
                    $data['image'] = $imp_image;
                    $data['video_url'] = $request->video_url ?? null;
                    $updatedata = $birthday->update($data);

                    $get = DB::table('birthdays')->where('birthdays.id', $id)->select('birthdays.id', 'birthdays.title', 'birthdays.description', 'birthdays.image', 'birthdays.video_url', 'cities.*')
                        ->join('cities', 'birthdays.city_id', 'cities.id')->get();

                    $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                    $get[0]->image = explode('|', $imp_image);
                    $get[0]->image = ($get[0]->image[0] != "") ? $get[0]->image : [];

                    return response()->json([
                        'success' => true,
                        'message' => 'Birthday Updated Successfully',
                        'data' => $get,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Record Not Exist',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Login as admin first',
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
        //
    }

    public function addbirthdayViaAdmin(Request $request)
    {
        // Log::info($request->all());
        try {
            if ($request->role_id == 1) {
                $validator = Validator::make($request->all(), [
                    'title'       => ['required', 'string'],
                    'description' => ['required'],
                    'city_id'     => ['required', 'numeric'],
                    'image'       => ['nullable'],
                    'image.*'     => ['mimes:jpeg,png,jpg'],
                    'video_url'   => ['nullable'],
                ]);

                if ($validator->fails()) {
                    return response()->json(['success' => false, 'message' => $validator->errors()]);
                }

                $city = City::where('id', $request->city_id)->first();
                if (!empty($city)) {
                    $images = array();
                    if ($files = $request->file('image')) {
                        foreach ($files as $file) {
                            $imgname = md5(rand('1000', '10000'));
                            $extension = strtolower($file->getClientOriginalExtension());
                            $img_full_name = $imgname . '.' . $extension;
                            $upload_path = 'public/birthday/';
                            $img_url = $upload_path . $img_full_name;
                            $file->move($upload_path, $img_full_name);
                            array_push($images, $img_url);
                        }
                    }
                    $imp_image =  implode('|', $images);

                    $birthday = Birthday::create([
                        'title' => $request->title,
                        'description' => $request->description,
                        'image' => $imp_image,
                        'video_url' => $request->video_url ?? null,
                        'user_id' => 1,
                        'city_id' => $request->city_id,
                        'status' => 1,
                    ]);
                    $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);

                    $exp = explode('|', $imp_image);
                    $birthday['image'] = ($exp[0] != "") ? $exp : [];

                    return response()->json([
                        'success' => true,
                        'message' => 'Added Successfully By Admin',
                        'data' => $birthday,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'City not exist',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Login as admin first',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }


    public function birthdayListOfUser()
    {
        try {
            $birthday = Birthday::whereDate('created_at', date('Y-m-d'))->orderBy('id', 'desc')->get()->toArray();
            if (!empty($birthday)) {
                $newarr = [];
                foreach ($birthday as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    $new['image'] = ($new['image'][0] != "") ? $new['image'] : [];
                    array_push($newarr, $new);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'List of today birthday',
                    'data' => $newarr,
                ]);
            } else {
                return response()->json([
                    'success' => true,
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

    public function acceptBirthday(Request $request)
    {
        // $auth_id = auth()->user()->id;
        try {

            if ($request->role_id == 1) {
                $validator = Validator::make($request->all(), [
                    'id' => ['required', 'numeric'],
                ]);

                if ($validator->fails()) {
                    return response()->json(['success' => false, 'message' => $validator->errors()]);
                }
                $id = $request->id;
                $birthday = Birthday::where('id', $id)->first();
                if (!empty($birthday)) {
                    $birthdaycity = Birthday::where('id', $id)->where('city_id', '!=', null)->first();
                    if (!empty($birthdaycity)) {
                        if ($birthday->status == 0) {
                            $birthday->status = 1;
                            $updateStatus = $birthday->update();
                            return response()->json([
                                'success' => true,
                                'message' => 'Request Accepted',
                            ]);
                        } elseif ($birthday->status == 1) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Request already accepted By Admin so you can not accept again',
                            ]);
                        }
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Please Add City First',
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Record Not Exist',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Login as admin first',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function denyBirthday(Request $request)
    {
        // $auth_id = auth()->user()->id;
        try {
            if ($request->role_id == 1) {
                $validator = Validator::make($request->all(), [
                    'id' => ['required', 'numeric'],
                ]);

                if ($validator->fails()) {
                    return response()->json(['success' => false, 'message' => $validator->errors()]);
                }
                $id = $request->id;
                $birthday = Birthday::where('id', $id)->first();
                if (!empty($birthday)) {
                    if ($birthday->status == 0) {
                        $birthday->status = 2;
                        $updateStatus = $birthday->update();
                        return response()->json([
                            'success' => true,
                            'message' => 'Request Denied',
                        ]);
                    } elseif ($birthday->status == 2) {
                        // if ($birthday->status == 1) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Request already denied By Admin so you can not deny again',
                        ]);
                        // } else {
                        //     return response()->json([
                        //         'message' => 'Renting product request is already denied By Admin',
                        //     ]);
                        // }
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Record Not Exist',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Login as admin first',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function showbBirthdayViacity(Request $request)
    {
        $city_id = $request->city_id;
        try {
            if ($city_id == null) {
                $birthday = Birthday::whereDate('created_at', date('Y-m-d'))->where('status', 1)->orderBy('id', 'desc')->get()->toArray();
                $newarr = [];
                foreach ($birthday as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    $new['image'] = ($new['image'][0] != "") ? $new['image'] : [];

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
                    'success' => true,
                    'message' => 'All birthday list',
                    'data' => $newarr,
                ]);
            } else {
                $city = City::where('id', $city_id)->first();
                if (!empty($city)) {
                    $birthday = Birthday::whereDate('birthdays.created_at', date('Y-m-d'))->where('birthdays.city_id', $city_id)->where('status', 1)
                        ->select('birthdays.*', 'users.name', 'cities.city_name')
                        ->join('users', 'users.id', 'birthdays.user_id')
                        ->join('cities', 'cities.id', 'birthdays.city_id')
                        ->orderBy('birthdays.id', 'desc')
                        ->get();
                    if (!empty($birthday)) {
                        $newarr = [];
                        foreach ($birthday as $key => $new) {
                            $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                            $new['image'] = explode('|', $new['image']);

                            $new['image'] = ($new['image'][0] != "") ? $new['image'] : [];


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
                            'success' => true,
                            'message' => 'Today Birthday list on the city basis',
                            'data' => $birthday,
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'No data Found',
                            'data' => [],
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
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
