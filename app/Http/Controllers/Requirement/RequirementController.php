<?php

namespace App\Http\Controllers\Requirement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\City;
use App\Models\Requirement;

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
                    'message' => 'List of Requirement',
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
                        'image.*'       => ['mimes:jpeg,png,jpg,svg']
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
                        'image.*'        => ['mimes:jpeg,png,jpg,svg']
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
                        'message' => 'Updated Successfully',
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
                    'message' => 'List of requirement',
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

    // public function addRequirementViaAdmin(Request $request)
    // {
    //     $id = auth()->user()->id;
    //     try {
    //         if ($id == 1) {
    //             $validator = Validator::make($request->all(), [
    //                 'title'          => ['required', 'string'],
    //                 'salary'         => ['required'],
    //                 'city_id'        => ['required', 'numeric'],
    //                 'working_time'   => ['required'],
    //                 'comment'        => ['required'],
    //             ]);

    //             if ($validator->fails()) {
    //                 return response()->json(['message' => $validator->errors()]);
    //             }

    //             $city = City::where('id', $request->city_id)->first();
    //             if (!empty($city)) {

    //                 $requirement = Requirement::create([
    //                     'title'          => $request->title,
    //                     'salary'         => $request->salary,
    //                     'city_id'        => $request->city_id,
    //                     'working_time'   => $request->working_time,
    //                     'comment'        => $request->comment,
    //                     'user_id'        => $id,
    //                     'status'         => 1,
    //                 ]);

    //                 return response()->json([
    //                     'message' => 'Requirement Added Successfully By Admin',
    //                     'data' => $requirement,
    //                 ]);
    //             } else {
    //                 return response()->json([
    //                     'error' => 'City not exist',
    //                 ]);
    //             }
    //         } else {
    //             return response()->json([
    //                 'message' => 'Login as admin first',
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => $e->getMessage(),
    //         ]);
    //     }
    // }

    // public function acceptRequirement(Request $request)
    // {
    //     $auth_id = auth()->user()->id;
    //     $id = $request->id;
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'id' => ['required', 'numeric'],
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json(['message' => $validator->errors()]);
    //         }
    //         $resume = Resume::where('id', $id)->first();
    //         if (!empty($resume)) {
    //             $resumecity = Resume::where('id', $id)->where('city_id', '!=', null)->first();
    //             if (!empty($resumecity)) {
    //                 if ($resume->status == 0) {
    //                     $resume->status = 1;
    //                     $updateStatus = $resume->update();
    //                     return response()->json([
    //                         'message' => 'Request is accepted By Admin',
    //                         'data' => $resume,
    //                     ]);
    //                 } else {
    //                     if ($resume->status == 2) {
    //                         return response()->json([
    //                             'message' => 'Request already denied By Admin so you can not accept',
    //                         ]);
    //                     } else {
    //                         return response()->json([
    //                             'message' => 'Request is already accepted By Admin',
    //                         ]);
    //                     }
    //                 }
    //             } else {
    //                 return response()->json([
    //                     'message' => 'Please add city first',
    //                 ]);
    //             }
    //         } else {
    //             return response()->json([
    //                 'message' => 'Record Not Exist',
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => $e->getMessage(),
    //         ]);
    //     }
    // }

    // public function denyRequirement(Request $request)
    // {
    //     $auth_id = auth()->user()->id;
    //     $id = $request->id;
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'id' => ['required', 'numeric'],
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json(['message' => $validator->errors()]);
    //         }
    //         $resume = Resume::where('id', $id)->first();
    //         if (!empty($resume)) {
    //             if ($resume->status == 0) {
    //                 $resume->status = 2;
    //                 $updateStatus = $resume->update();
    //                 return response()->json([
    //                     'message' => 'Request denied By Admin',
    //                     'data' => $resume,
    //                 ]);
    //             } else {
    //                 if ($resume->status == 1) {
    //                     return response()->json([
    //                         'message' => 'Request already accepted By Admin so you can not deny',
    //                     ]);
    //                 } else {
    //                     return response()->json([
    //                         'message' => 'Renting product request is already denied By Admin',
    //                     ]);
    //                 }
    //             }
    //         } else {
    //             return response()->json([
    //                 'message' => 'Record Not Exist',
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => $e->getMessage(),
    //         ]);
    //     }
    // }

    // public function showbRequirementViacity(Request $request)
    // {
    //     $city_id = $request->city_id;
    //     try {
    //         if ($city_id == null) {
    //             $resume = Resume::where('status', 1)->orderBy('id', 'desc')->get()->toArray();
    //             foreach ($resume as $key => $new) {
    //                 $new['pdf'] = str_replace("public", env('APP_URL') . "public", $new['pdf']);
    //             }
    //             return response()->json([
    //                 'message' => 'All resume list',
    //                 'data' => $new,
    //             ]);
    //         } else {
    //             $city = City::where('id', $city_id)->first();
    //             if (!empty($city)) {
    //                 $resume = Resume::where('resumes.city_id', $city_id)->where('status', 1)
    //                     ->select('resumes.*', 'users.name', 'cities.city_name')
    //                     ->join('users', 'users.id', 'resumes.user_id')
    //                     ->join('cities', 'cities.id', 'resumes.city_id')
    //                     ->orderBy('resumes.id', 'desc')
    //                     ->get();
    //                 if (!empty($resume)) {
    //                     $newarr = [];
    //                     foreach ($resume as $key => $new) {
    //                         $new['pdf'] = str_replace("public", env('APP_URL') . "public", $new['pdf']);
    //                     }
    //                     return response()->json([
    //                         'message' => 'Resume list on the city basis',
    //                         'data' => $new,
    //                     ]);
    //                 } else {
    //                     return response()->json([
    //                         'error' => 'No data Found',
    //                     ]);
    //                 }
    //             } else {
    //                 return response()->json([
    //                     'message' => 'City Not Exist',
    //                 ]);
    //             }
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => $e->getMessage(),
    //         ]);
    //     }
    // }
}
