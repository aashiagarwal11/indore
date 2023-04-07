<?php

namespace App\Http\Controllers\KrishiMandiBhav;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\City;
use App\Models\KrishiMandiBhav;
use App\Models\Advertisment;



class KrishiMandiBhavController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $KrishiMandiBhav = KrishiMandiBhav::orderBy('id', 'desc')->get();
            if (!empty($KrishiMandiBhav)) {
                $KrishiMandiBhavarr = [];
                foreach ($KrishiMandiBhav as $key => $Krishi) {
                    $Krishi['image'] = str_replace("public", env('APP_URL') . "public", $Krishi['image']);
                    $Krishi['image'] = explode('|', $Krishi['image']);
                    $Krishi['image'] = ($Krishi['image'][0] != "") ? $Krishi['image'] : [];
                    array_push($KrishiMandiBhavarr, $Krishi);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'List',
                    'data' => $KrishiMandiBhavarr,
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'No data found',
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
        ## add krishi mandi bhav by admin
        $id = auth()->user()->id;
        try {
            if ($id == 1) {
                $validator = Validator::make($request->all(), [
                    'title'       => ['required', 'string'],
                    'description' => ['required'],
                    'city_id'     => ['required', 'numeric'],
                    'image'       => ['nullable'],
                    'image.*'     => ['mimes:jpeg,png,jpg'],
                    'video_url'   => ['nullable'],
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()]);
                }

                $city = City::where('id', $request->city_id)->first();
                if (!empty($city)) {
                    $images = array();
                    if ($files = $request->file('image')) {
                        foreach ($files as $file) {
                            $imgname = md5(rand('1000', '10000'));
                            $extension = strtolower($file->getClientOriginalExtension());
                            $img_full_name = $imgname . '.' . $extension;
                            $upload_path = 'public/krishiMandiImage/';
                            $img_url = $upload_path . $img_full_name;
                            $file->move($upload_path, $img_full_name);
                            array_push($images, $img_url);
                        }
                    }
                    $imp_image =  implode('|', $images);
                    $krishiMandiBhav = KrishiMandiBhav::create([
                        'title' => $request->title,
                        'description' => $request->description,
                        'city_id' => $request->city_id,
                        'image' => $imp_image,
                        'user_id' => $id,
                        'video_url' => $request->video_url,
                    ]);
                    $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                    $exp = explode('|', $imp_image);

                    $krishiMandiBhav['image'] = ($exp[0] != "") ? $exp : [];

                    return response()->json([
                        'status' => true,
                        'message' => 'Added Successfully',
                        'data' => $krishiMandiBhav,
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
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $specificbhav = KrishiMandiBhav::where('krishi_mandi_bhavs.id', $id)
                ->select('krishi_mandi_bhavs.id', 'krishi_mandi_bhavs.title', 'users.name', 'cities.city_name', 'krishi_mandi_bhavs.description', 'krishi_mandi_bhavs.image', 'krishi_mandi_bhavs.video_url', 'krishi_mandi_bhavs.created_at', 'krishi_mandi_bhavs.updated_at')
                ->join('users', 'users.id', 'krishi_mandi_bhavs.user_id')
                ->join('cities', 'cities.id', 'krishi_mandi_bhavs.city_id')
                ->get();
            if (!empty($specificbhav)) {
                $newarr = [];
                foreach ($specificbhav as $key => $bhav) {
                    $bhav['image'] = str_replace("public", env('APP_URL') . "public", $bhav['image']);
                    $bhav['image'] = explode('|', $bhav['image']);
                    $bhav['image'] = ($bhav['image'][0] != "") ? $bhav['image'] : [];
                    array_push($newarr, $bhav);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Detail with specific Id',
                    'data' => $newarr,
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'No data Exist',
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $auth_id = auth()->user()->id;
        try {
            if ($auth_id == 1) {
                $KrishiMandiBhav = KrishiMandiBhav::where('id', $id)->first();
                if (!empty($KrishiMandiBhav)) {
                    $validator = Validator::make($request->all(), [
                        'title'       => ['required', 'string'],
                        'description' => ['required'],
                        'city_id'     => ['required', 'numeric'],
                        'image'       => ['nullable'],
                        'image.*'     => ['mimes:jpeg,png,jpg'],
                        'video_url'   => ['nullable'],
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
                            $upload_path = 'public/krishiMandiImage/';
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
                    $updatedata = $KrishiMandiBhav->update($data);

                    $specificbhav = KrishiMandiBhav::where('krishi_mandi_bhavs.id', $id)
                        ->select('krishi_mandi_bhavs.id', 'krishi_mandi_bhavs.title', 'cities.city_name', 'krishi_mandi_bhavs.description', 'krishi_mandi_bhavs.image', 'krishi_mandi_bhavs.video_url', 'krishi_mandi_bhavs.created_at', 'krishi_mandi_bhavs.updated_at')
                        ->join('cities', 'cities.id', 'krishi_mandi_bhavs.city_id')
                        ->get();

                    $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);

                    $specificbhav[0]->image = explode('|', $imp_image);
                    $specificbhav[0]->image = ($specificbhav[0]->image[0] != "") ? $specificbhav[0]->image : [];

                    return response()->json([
                        'status' => true,
                        'message' => 'Updated Successfully',
                        'data' => $specificbhav,
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
        try {
            $delete = KrishiMandiBhav::where('id', $id)->first();
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


    public function showListViaCity(Request $request)
    {
        ## showing the renting product list on the city basis

        $city_id = $request->city_id;
        try {
            if ($city_id == null) {
                $bhavproduct = KrishiMandiBhav::orderBy('id', 'desc')->get();
                $newarr = [];
                foreach ($bhavproduct as $key => $bhav) {
                    $bhav['image'] = str_replace("public", env('APP_URL') . "public", $bhav['image']);
                    $bhav['image'] = explode('|', $bhav['image']);
                    $bhav['image'] = ($bhav['image'][0] != "") ? $bhav['image'] : [];

                    ## random ads
                    $ads = Advertisment::all()->random(1);
                    if (!empty($ads)) {
                        $image = explode('|', $ads[0]->ads_image);
                        shuffle($image);

                        $ads[0]->ads_image = $image[0];
                        $ads[0]->ads_image = str_replace("public", env('APP_URL') . "public", $ads[0]->ads_image);
                    }

                    $bhav['randomimage'] = $ads[0]->ads_image;
                    array_push($newarr, $bhav);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'List via city basis',
                    'data' => $newarr,
                ]);
            } else {
                $city = City::where('id', $city_id)->first();
                if (!empty($city)) {
                    $bhavproduct = KrishiMandiBhav::where('krishi_mandi_bhavs.city_id', $city_id)
                        ->select('krishi_mandi_bhavs.*', 'cities.city_name')
                        ->join('cities', 'cities.id', 'krishi_mandi_bhavs.city_id')
                        ->orderBy('krishi_mandi_bhavs.id', 'desc')
                        ->get();
                    if (!empty($bhavproduct)) {
                        $newarr = [];
                        foreach ($bhavproduct as $key => $bhav) {
                            $bhav['image'] = str_replace("public", env('APP_URL') . "public", $bhav['image']);
                            $bhav['image'] = explode('|', $bhav['image']);

                            $bhav['image'] = ($bhav['image'][0] != "") ? $bhav['image'] : [];

                            ## random ads
                            $ads = Advertisment::all()->random(1);
                            if (!empty($ads)) {
                                $image = explode('|', $ads[0]->ads_image);
                                shuffle($image);

                                $ads[0]->ads_image = $image[0];
                                $ads[0]->ads_image = str_replace("public", env('APP_URL') . "public", $ads[0]->ads_image);
                            }

                            $bhav['randomimage'] = $ads[0]->ads_image;
                            array_push($newarr, $bhav);
                        }
                        return response()->json([
                            'status' => true,
                            'message' => 'List on the city basis',
                            'data' => $bhavproduct,
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
