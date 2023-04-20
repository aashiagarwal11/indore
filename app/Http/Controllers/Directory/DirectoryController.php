<?php

namespace App\Http\Controllers\Directory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Directory;
use Illuminate\Support\Facades\DB;
use App\Models\City;
use App\Models\Advertisment;


class DirectoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $directory = Directory::where('status', 1)->orderBy('id', 'desc')->get()->toArray();
            if (!empty($directory)) {
                $newarr = [];
                foreach ($directory as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    $new['image'] = ($new['image'][0] != "") ? $new['image'] : [];
                    array_push($newarr, $new);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'List',
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
        $id = auth()->user()->id;
        $role_id = auth()->user()->role_id;
        try {
            if (!empty($id)) {
                if ($role_id != 1) {
                    $validator = Validator::make($request->all(), [
                        'biz_name'     => ['required', 'string'],
                        'contact_per1' => ['nullable', 'string'],
                        'number1'      => ['nullable'],
                        'category'     => ['nullable', 'string'],
                        'city'         => ['nullable', 'string'],
                        'state'        => ['nullable', 'string'],
                        'contact_per2' => ['nullable', 'string'],
                        'contact_per3' => ['nullable', 'string'],
                        'number2'      => ['nullable'],
                        'number3'      => ['nullable'],
                        'address'      => ['nullable', 'string'],
                        'detail'       => ['nullable', 'string'],
                        // 'image'        => ['nullable'],
                        'image.*'      => ['nullable', 'mimes:jpeg,png,jpg'],
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['status' => false, 'message' => $validator->errors()]);
                    }

                    $images = array();
                    if ($files = $request->file('image')) {
                        foreach ($files as $file) {
                            $imgname = md5(rand('1000', '10000'));
                            $extension = strtolower($file->getClientOriginalExtension());
                            $img_full_name = $imgname . '.' . $extension;
                            $upload_path = 'public/directory/';
                            $img_url = $upload_path . $img_full_name;
                            $file->move($upload_path, $img_full_name);
                            array_push($images, $img_url);
                        }
                    }

                    $imp_image =  implode('|', $images);

                    $directory = Directory::create([
                        'biz_name' => $request->biz_name,
                        'contact_per1' => $request->contact_per1,
                        'number1' => $request->number1,
                        'category' => $request->category,
                        'city' => $request->city,
                        'state' => $request->state,
                        'contact_per2' => $request->contact_per2,
                        'contact_per3' => $request->contact_per3,
                        'number2' => $request->number2,
                        'number3' => $request->number3,
                        'address' => $request->address,
                        'detail' => $request->detail,
                        'user_id' => $id,
                        'image' => $imp_image,
                    ]);
                    $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                    $exp = explode('|',  $imp_image);

                    $directory['image'] = ($exp[0] != "") ? $exp : [];


                    return response()->json([
                        'status' => true,
                        'message' => 'Added Successfully',
                        'data' => $directory,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Login as user',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
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
        $auth_id = auth()->user()->id;
        try {
            if ($auth_id == 1) {
                $directory = Directory::where('id', $id)->first();
                if (!empty($directory)) {
                    $validator = Validator::make($request->all(), [
                        'city_id'      => ['required', 'numeric'],
                        'biz_name'     => ['required', 'string'],
                        'contact_per1' => ['nullable', 'string'],
                        'number1'      => ['nullable'],
                        'category'     => ['nullable', 'string'],
                        'city'         => ['nullable', 'string'],
                        'state'        => ['nullable', 'string'],
                        'contact_per2' => ['nullable', 'string'],
                        'contact_per3' => ['nullable', 'string'],
                        'number2'      => ['nullable'],
                        'number3'      => ['nullable'],
                        'address'      => ['nullable', 'string'],
                        'detail'       => ['nullable', 'string'],
                        'image'        => ['nullable'],
                        'image.*'      => ['mimes:jpeg,png,jpg'],
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['status' => false, 'message' => $validator->errors()]);
                    }
                    $images = array();
                    if ($files = $request->file('image')) {
                        foreach ($files as $file) {
                            $imgname = md5(rand('1000', '10000'));
                            $extension = strtolower($file->getClientOriginalExtension());
                            $img_full_name = $imgname . '.' . $extension;
                            $upload_path = 'public/directory/';
                            $img_url = $upload_path . $img_full_name;
                            $file->move($upload_path, $img_full_name);
                            array_push($images, $img_url);
                        }
                    }

                    $imp_image =  implode('|', $images);

                    $data['city_id'] = $request->city_id;
                    $data['biz_name'] = $request->biz_name;
                    $data['contact_per1'] = $request->contact_per1;
                    $data['number1'] = $request->number1;
                    $data['category'] = $request->category;
                    $data['city'] = $request->city;
                    $data['state'] = $request->state;
                    $data['contact_per2'] = $request->contact_per2;
                    $data['contact_per3'] = $request->contact_per3;
                    $data['number2'] = $request->number2;
                    $data['number3'] = $request->number3;
                    $data['address'] = $request->address;
                    $data['detail'] = $request->detail;
                    $data['image'] = $imp_image;
                    $updatedata = $directory->update($data);

                    // $get = DB::table('directories')->where('id', $id)->get();


                    $get = DB::table('directories')->where('directories.id', $id)
                        ->join('cities', 'directories.city_id', 'cities.id')->get();

                    $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                    $get[0]->image = explode('|', $imp_image);

                    $get[0]->image = ($get[0]->image[0] != "") ? $get[0]->image : [];

                    return response()->json([
                        'status' => true,
                        'message' => 'Updated Successfully',
                        'data' => $get,
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

    public function addDirectoryViaAdmin(Request $request)
    {
        $id = auth()->user()->id;
        $role_id = auth()->user()->role_id;
        try {
            if (!empty($id)) {
                if ($role_id == 1) {
                    $validator = Validator::make($request->all(), [
                        'city_id'      => ['required', 'numeric'],
                        'biz_name'     => ['required', 'string'],
                        'contact_per1' => ['nullable', 'string'],
                        'number1'      => ['nullable'],
                        'category'     => ['nullable', 'string'],
                        'city'         => ['nullable', 'string'],
                        'state'        => ['nullable', 'string'],
                        'contact_per2' => ['nullable', 'string'],
                        'contact_per3' => ['nullable', 'string'],
                        'number2'      => ['nullable'],
                        'number3'      => ['nullable'],
                        'address'      => ['nullable', 'string'],
                        'detail'       => ['nullable', 'string'],
                        'image'        => ['nullable'],
                        'image.*'      => ['mimes:jpeg,png,jpg'],
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['status' => false, 'message' => $validator->errors()]);
                    }

                    $images = array();
                    if ($files = $request->file('image')) {
                        foreach ($files as $file) {
                            $imgname = md5(rand('1000', '10000'));
                            $extension = strtolower($file->getClientOriginalExtension());
                            $img_full_name = $imgname . '.' . $extension;
                            $upload_path = 'public/directory/';
                            $img_url = $upload_path . $img_full_name;
                            $file->move($upload_path, $img_full_name);
                            array_push($images, $img_url);
                        }
                    }

                    $imp_image =  implode('|', $images);

                    $city = City::where('id', $request->city_id)->first();
                    if (!empty($city)) {

                        $directory = Directory::create([
                            'biz_name' => $request->biz_name,
                            'city_id' => $request->city_id,
                            'contact_per1' => $request->contact_per1,
                            'number1' => $request->number1,
                            'category' => $request->category,
                            'city' => $request->city,
                            'state' => $request->state,
                            'contact_per2' => $request->contact_per2,
                            'contact_per3' => $request->contact_per3,
                            'number2' => $request->number2,
                            'number3' => $request->number3,
                            'address' => $request->address,
                            'detail' => $request->detail,
                            'user_id' => $id,
                            'image' => $imp_image,
                            'status' => 1,
                        ]);
                        $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                        $exp = explode('|',  $imp_image);

                        $directory['image'] = ($exp[0] != "") ? $exp : [];

                        return response()->json([
                            'status' => true,
                            'message' => 'Added Successfully By Admin',
                            'data' => $directory,
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'City not exist',
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
                    'message' => 'Login as user',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function directoryListOfUser()
    {
        try {
            $directory = Directory::orderBy('id', 'desc')->get()->toArray();
            if (!empty($directory)) {
                $newarr = [];
                foreach ($directory as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    $new['image'] = ($new['image'][0] != "") ? $new['image'] : [];
                    array_push($newarr, $new);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'List',
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

    public function acceptDirectory(Request $request)
    {
        // $auth_id = auth()->user()->id;
        try {

            if ($request->role_id == 1) {

                $validator = Validator::make($request->all(), [
                    'id' => ['required', 'numeric'],
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()]);
                }
                $id = $request->id;
                $directory = Directory::where('id', $id)->first();
                if (!empty($directory)) {
                    $birthdaycity = Directory::where('id', $id)->where('city_id', '!=', null)->first();
                    if (!empty($birthdaycity)) {
                        if ($directory->status == 0) {
                            $directory->status = 1;
                            $updateStatus = $directory->update();
                            return response()->json([
                                'status' => true,
                                'message' => 'Request Accepted',
                                'data' => $directory,
                            ]);
                        } elseif ($directory->status == 1) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Request already accepted By Admin so you can not accept again',
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Please Add City First',
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
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

    public function denyDirectory(Request $request)
    {
        // $auth_id = auth()->user()->id;

        try {
            if ($request->role_id == 1) {
                $validator = Validator::make($request->all(), [
                    'id' => ['required', 'numeric'],
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()]);
                }
                $id = $request->id;
                $directory = Directory::where('id', $id)->first();
                if (!empty($directory)) {
                    if ($directory->status == 0) {
                        $directory->status = 2;
                        $updateStatus = $directory->update();
                        return response()->json([
                            'status' => true,
                            'message' => 'Request Denied',
                            'data' => $directory,
                        ]);
                    } elseif ($directory->status == 2) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Request already denied By Admin so you can not deny again',
                        ]);
                    }
                } else {
                    return response()->json([
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

    public function showDirectoryViacity(Request $request)
    {
        $city_id = $request->city_id;
        try {
            if ($city_id == null) {
                $directory = Directory::where('status', 1)->orderBy('id', 'desc')->get()->toArray();

                $newarr = [];
                foreach ($directory as $key => $new) {
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
                    'status' => true,
                    'message' => 'All directory list',
                    'data' => $newarr,
                ]);
            } else {
                $city = City::where('id', $city_id)->first();
                if (!empty($city)) {
                    $directory = Directory::where('directories.city_id', $city_id)->where('directories.status', 1)
                        ->select('directories.*', 'users.name', 'cities.city_name')
                        ->join('users', 'users.id', 'directories.user_id')
                        ->join('cities', 'cities.id', 'directories.city_id')
                        ->orderBy('directories.id', 'desc')
                        ->get();
                    if (!empty($directory)) {
                        $newarr = [];
                        foreach ($directory as $key => $new) {
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
                            'status' => true,
                            'message' => 'Directory list on the city basis',
                            'data' => $newarr,
                        ]);
                    } else {
                        return response()->json([
                            'status' => true,
                            'message' => 'No data Found',
                            'data' => [],

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
}
