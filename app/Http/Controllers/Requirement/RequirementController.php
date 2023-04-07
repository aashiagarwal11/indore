<?php

namespace App\Http\Controllers\Requirement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\City;
use App\Models\Requirement;
use App\Models\Advertisment;

class RequirementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $requirement = Requirement::where('status', 1)->orderBy('id', 'desc')->get()->toArray();
            if (!empty($requirement)) {
                $newarr = [];
                foreach ($requirement as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    array_push($newarr, $new);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'List of Requirement',
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
                        'title'         => ['required', 'string'],
                        'salary'        => ['required'],
                        'working_time'  => ['required'],
                        'comment'       => ['required'],
                        'image'         => ['required'],
                        'image.*'       => ['mimes:jpeg,png,jpg']
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
                            $upload_path = 'public/requirement/';
                            $img_url = $upload_path . $img_full_name;
                            $file->move($upload_path, $img_full_name);
                            array_push($images, $img_url);
                        }
                    }

                    $imp_image =  implode('|', $images);



                    $requirement = Requirement::create([
                        'title'        => $request->title,
                        'salary'       => $request->salary,
                        'user_id'      => $id,
                        'city_id'      => $request->city_id ?? null,
                        'working_time' => $request->working_time,
                        'comment'      => $request->comment,
                        'image'        => $imp_image,
                    ]);

                    $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                    $exp = explode('|',  $imp_image);

                    $data['title'] = $requirement->title;
                    $data['salary'] = $requirement->salary;
                    $data['working_time'] = $requirement->working_time;
                    $data['comment'] = $requirement->comment;
                    $data['image'] = $exp;
                    $data['created_at'] = $requirement->created_at;
                    $data['updated_at'] = $requirement->updated_at;

                    return response()->json([
                        'status' => true,
                        'message' => 'Added Successfully',
                        'data' => $data,
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
                $requirement = Requirement::where('id', $id)->first();
                if (!empty($requirement)) {
                    $validator = Validator::make($request->all(), [
                        'title'          => ['required', 'string'],
                        'salary'         => ['required'],
                        'city_id'        => ['required', 'numeric'],
                        'working_time'   => ['required'],
                        'comment'        => ['required'],
                        'image'          => ['required'],
                        'image.*'        => ['mimes:jpeg,png,jpg']
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
                            $upload_path = 'public/requirement/';
                            $img_url = $upload_path . $img_full_name;
                            $file->move($upload_path, $img_full_name);
                            array_push($images, $img_url);
                        }
                    }

                    $imp_image =  implode('|', $images);

                    $data['title']        = $request->title;
                    $data['salary']       = $request->salary;
                    $data['city_id']      = $request->city_id;
                    $data['working_time'] = $request->working_time;
                    $data['comment']      = $request->comment;
                    $data['image']        = $imp_image;

                    $updatedata = $requirement->update($data);

                    $get = DB::table('requirements')->where('requirements.id', $id)->select('requirements.id', 'requirements.title', 'requirements.salary', 'requirements.working_time', 'requirements.comment', 'requirements.image', 'cities.*')
                        ->join('cities', 'requirements.city_id', 'cities.id')->get();

                    $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                    $get[0]->image = explode('|', $imp_image);

                    return response()->json([
                        'status' => true,
                        'message' => 'Updated Successfully',
                        'data' => $get,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Record Not Exist',
                        'data' => [],
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

    public function requirementListOfUser()
    {
        try {
            $requirement = Requirement::orderBy('id', 'desc')->get()->toArray();
            if (!empty($requirement)) {

                $newarr = [];
                foreach ($requirement as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    array_push($newarr, $new);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'List of requirement',
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

    public function addRequirementViaAdmin(Request $request)
    {
        $id = auth()->user()->id;
        try {
            if ($id == 1) {
                $validator = Validator::make($request->all(), [
                    'title'          => ['required', 'string'],
                    'salary'         => ['required'],
                    'city_id'        => ['required', 'numeric'],
                    'working_time'   => ['required'],
                    'comment'        => ['required'],
                    'image'          => ['required'],
                    'image.*'        => ['mimes:jpeg,png,jpg']
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
                        $upload_path = 'public/requirement/';
                        $img_url = $upload_path . $img_full_name;
                        $file->move($upload_path, $img_full_name);
                        array_push($images, $img_url);
                    }
                }

                $imp_image =  implode('|', $images);

                $city = City::where('id', $request->city_id)->first();
                if (!empty($city)) {

                    $requirement = Requirement::create([
                        'title'          => $request->title,
                        'salary'         => $request->salary,
                        'city_id'        => $request->city_id,
                        'working_time'   => $request->working_time,
                        'comment'        => $request->comment,
                        'user_id'        => $id,
                        'status'         => 1,
                        'image'          => $imp_image,
                    ]);

                    return response()->json([
                        'status' => true,
                        'message' => 'Requirement Added Successfully By Admin',
                        'data' => $requirement,
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

    public function acceptRequirement(Request $request)
    {
        $auth_id = auth()->user()->id;
        $id = $request->id;
        try {
            $validator = Validator::make($request->all(), [
                'id' => ['required', 'numeric'],
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()]);
            }
            $requirement = Requirement::where('id', $id)->first();
            if (!empty($requirement)) {
                $requirementcity = Requirement::where('id', $id)->where('city_id', '!=', null)->first();
                if (!empty($requirementcity)) {
                    if ($requirement->status == 0) {
                        $requirement->status = 1;
                        $updateStatus = $requirement->update();
                        return response()->json([
                            'status' => true,
                            'message' => 'Request is accepted By Admin',
                            'data' => $requirement,
                        ]);
                    } elseif ($requirement->status == 2) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Request already denied By Admin so you can not accept',
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

    public function denyRequirement(Request $request)
    {
        $auth_id = auth()->user()->id;
        $id = $request->id;
        try {
            $validator = Validator::make($request->all(), [
                'id' => ['required', 'numeric'],
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()]);
            }
            $requirement = Requirement::where('id', $id)->first();
            if (!empty($requirement)) {
                if ($requirement->status == 0) {
                    $requirement->status = 2;
                    $updateStatus = $requirement->update();
                    return response()->json([
                        'status' => true,
                        'message' => 'Request denied By Admin',
                        'data' => $requirement,
                    ]);
                } elseif ($requirement->status == 1) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Request already accepted By Admin so you can not deny',
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

    public function showRequirementViacity(Request $request)
    {
        $city_id = $request->city_id;
        try {
            if ($city_id == null) {
                $requirement = Requirement::where('status', 1)->orderBy('id', 'desc')->get()->toArray();

                $newarr = [];
                foreach ($requirement as $key => $new) {
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
                    'message' => 'All requirement list',
                    'data' => $newarr,
                ]);
            } else {
                $city = City::where('id', $city_id)->first();
                if (!empty($city)) {
                    $requirement = Requirement::where('requirements.city_id', $city_id)->where('status', 1)
                        ->select('requirements.*', 'users.name', 'cities.city_name')
                        ->join('users', 'users.id', 'requirements.user_id')
                        ->join('cities', 'cities.id', 'requirements.city_id')
                        ->orderBy('requirements.id', 'desc')
                        ->get();
                    if (!empty($requirement)) {
                        $newarr = [];
                        foreach ($requirement as $key => $new) {
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
                            'message' => 'Requirement list on the city basis',
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
