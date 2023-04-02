<?php

namespace App\Http\Controllers\ShokSuchna;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\ShokSuchna;
use Illuminate\Support\Facades\DB;
use App\Models\City;

class ShokSuchnaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ## showing the news which is accepted by admin
        try {
            $shoksuchna = ShokSuchna::where('status', 1)->where('city_id', '!=', null)->orderBy('id', 'desc')->get()->toArray();
            if (!empty($shoksuchna)) {
                $newarr = [];
                foreach ($shoksuchna as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    array_push($newarr, $new);
                }
                return response()->json([
                    'message' => 'All list',
                    'data' => $newarr,
                ]);
            } else {
                return response()->json([
                    'error' => 'No data Found',
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
        ## store details by user side
        $id = auth()->user()->id;
        $role_id = auth()->user()->role_id;
        try {
            if (!empty($id)) {
                if ($role_id != 1) {
                    $validator = Validator::make($request->all(), [
                        'title' => ['required', 'string'],
                        'description' => ['required', 'string'],
                        'image' => ['required'],
                        'image.*' => ['mimes:jpeg,png,jpg,svg']
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['message' => $validator->errors()]);
                    }

                    $images = array();
                    if ($files = $request->file('image')) {
                        foreach ($files as $file) {
                            $imgname = md5(rand('1000', '10000'));
                            $extension = strtolower($file->getClientOriginalExtension());
                            $img_full_name = $imgname . '.' . $extension;
                            $upload_path = 'public/shoksuchna/';
                            $img_url = $upload_path . $img_full_name;
                            $file->move($upload_path, $img_full_name);
                            array_push($images, $img_url);
                        }
                    }


                    $imp_image =  implode('|', $images);

                    $shoksuchna = ShokSuchna::create([
                        'title' => $request->title,
                        'description' => $request->description,
                        'user_id' => $id,
                        'city_id' => $request->city_id ?? null,
                        'image' => $imp_image,
                    ]);
                    $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                    $exp = explode('|',  $imp_image);

                    $data['title'] = $shoksuchna->title;
                    $data['description'] = $shoksuchna->description;
                    $data['image'] = $exp;
                    $data['created_at'] = $shoksuchna->created_at;
                    $data['updated_at'] = $shoksuchna->updated_at;

                    return response()->json([
                        'message' => 'Added Successfully',
                        'data' => $data,
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Login as user',
                    ]);
                }
            } else {
                return response()->json([
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
        $auth_id = auth()->user()->id;
        try {
            if ($auth_id == 1) {
                $shoksuchna = ShokSuchna::where('id', $id)->first();
                if (!empty($shoksuchna)) {
                    $validator = Validator::make($request->all(), [
                        'title' => ['required', 'string'],
                        'description' => ['required', 'string'],
                        'city_id' => ['required', 'numeric'],
                        'image' => ['required'],
                        'image.*' => ['mimes:jpeg,png,jpg,svg']
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['message' => $validator->errors()]);
                    }
                    $images = array();
                    if ($files = $request->file('image')) {
                        foreach ($files as $file) {
                            $imgname = md5(rand('1000', '10000'));
                            $extension = strtolower($file->getClientOriginalExtension());
                            $img_full_name = $imgname . '.' . $extension;
                            $upload_path = 'public/shoksuchna/';
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
                    $updatedata = $shoksuchna->update($data);

                    $get = DB::table('shok_suchnas')->where('shok_suchnas.id', $id)->select('shok_suchnas.id', 'shok_suchnas.title', 'shok_suchnas.description', 'shok_suchnas.image', 'cities.*')
                        ->join('cities', 'shok_suchnas.city_id', 'cities.id')->get();

                    $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                    $get[0]->image = explode('|', $imp_image);

                    return response()->json([
                        'message' => 'Record Updated Successfully',
                        'data' => $get,
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Record Not Exist',
                    ]);
                }
            } else {
                return response()->json([
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

    public function addViaAdmin(Request $request)
    {
        ## add news by admin
        $id = auth()->user()->id;
        try {
            if ($id == 1) {
                $validator = Validator::make($request->all(), [
                    'title' => ['required', 'string'],
                    'description' => ['required', 'string'],
                    'city_id' => ['required', 'numeric'],
                    'image' => ['required'],
                    'image.*' => ['mimes:jpeg,png,jpg,svg'],
                ]);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()]);
                }

                $city = City::where('id', $request->city_id)->first();
                if (!empty($city)) {
                    $images = array();
                    if ($files = $request->file('image')) {
                        foreach ($files as $file) {
                            $imgname = md5(rand('1000', '10000'));
                            $extension = strtolower($file->getClientOriginalExtension());
                            $img_full_name = $imgname . '.' . $extension;
                            $upload_path = 'public/shoksuchna/';
                            $img_url = $upload_path . $img_full_name;
                            $file->move($upload_path, $img_full_name);
                            array_push($images, $img_url);
                        }
                    }
                    $imp_image =  implode('|', $images);
                    $shoksuchna = ShokSuchna::create([
                        'title' => $request->title,
                        'description' => $request->description,
                        'image' => $imp_image,
                        'city_id' => $request->city_id ?? null,
                        'status' => 1,
                        'user_id' => $id,
                    ]);
                    $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                    $exp = explode('|', $imp_image);
                    $data['title'] = $request->title;
                    $data['description'] = $request->description;
                    $data['city_id'] = $request->city_id;
                    $data['image'] = $exp;
                    $data['user_id'] = $id;

                    return response()->json([
                        'message' => 'Added Successfully By Admin',
                        'data' => $data,
                    ]);
                } else {
                    return response()->json([
                        'error' => 'City not exist',
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Login as admin first',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function showAllOnAdmin()
    {
        try {
            $shoksuchna = ShokSuchna::get()->toArray();
            // dd($shoksuchna);
            if (!empty($shoksuchna)) {
                $newarr = [];
                foreach ($shoksuchna as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    array_push($newarr, $new);
                }
                return response()->json([
                    'message' => 'All List',
                    'data' => $newarr,
                ]);
            } else {
                return response()->json([
                    'error' => 'No data Found',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function acceptShokSuchna(Request $request)
    {
        $auth_id = auth()->user()->id;
        $id = $request->id;
        try {
            $validator = Validator::make($request->all(), [
                'id' => ['required', 'numeric'],
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()]);
            }
            $shoksuchna = ShokSuchna::where('id', $id)->first();
            if (!empty($shoksuchna)) {
                $shokSuchnacity = ShokSuchna::where('id', $id)->where('city_id', '!=', null)->first();
                if (!empty($shokSuchnacity)) {
                    if ($shoksuchna->status == 0) {
                        $shoksuchna->status = 1;
                        $updateStatus = $shoksuchna->update();
                        return response()->json([
                            'message' => 'Accepted By Admin',
                            'data' => $shoksuchna,
                        ]);
                    } else {
                        if ($shoksuchna->status == 2) {
                            return response()->json([
                                'message' => 'Request already denied By Admin so you can not accept',
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

    public function denyShokSuchna(Request $request)
    {
        $auth_id = auth()->user()->id;
        $id = $request->id;
        try {
            $validator = Validator::make($request->all(), [
                'id' => ['required', 'numeric'],
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()]);
            }
            $shokSuchna = ShokSuchna::where('id', $id)->first();
            if (!empty($shokSuchna)) {
                if ($shokSuchna->status == 0) {
                    $shokSuchna->status = 2;
                    $updateStatus = $shokSuchna->update();
                    return response()->json([
                        'message' => 'Request denied By Admin',
                        'data' => $shokSuchna,
                    ]);
                } else {
                    if ($shokSuchna->status == 1) {
                        return response()->json([
                            'message' => 'Request already accepted By Admin so you can not deny',
                        ]);
                    } else {
                        return response()->json([
                            'message' => 'Request is already denied By Admin',
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

    public function showListViacity(Request $request)
    {
        $city_id = $request->city_id;
        try {
            if ($city_id == null) {
                $shokSuchna = ShokSuchna::where('status', 1)->orderBy('id', 'desc')->get()->toArray();
                $newarr = [];
                foreach ($shokSuchna as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    array_push($newarr, $new);
                }
                return response()->json([
                    'message' => 'All list',
                    'data' => $newarr,
                ]);
            } else {
                $city = City::where('id', $city_id)->first();
                if (!empty($city)) {
                    $shokSuchna = ShokSuchna::where('shok_suchnas.city_id', $city_id)->where('status', 1)
                        ->select('shok_suchnas.*', 'users.name', 'cities.city_name')
                        ->join('users', 'users.id', 'shok_suchnas.user_id')
                        ->join('cities', 'cities.id', 'shok_suchnas.city_id')
                        ->orderBy('shok_suchnas.id', 'desc')
                        ->get();
                    if (!empty($shokSuchna)) {
                        $newarr = [];
                        foreach ($shokSuchna as $key => $new) {
                            $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                            $new['image'] = explode('|', $new['image']);
                            array_push($newarr, $new);
                        }
                        return response()->json([
                            'message' => 'List on the city basis',
                            'data' => $shokSuchna,
                        ]);
                    } else {
                        return response()->json([
                            'error' => 'No data Found',
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
