<?php

namespace App\Http\Controllers\Resume;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resume;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\City;
use App\Models\Advertisment;


class ResumeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $resume = Resume::where('status', 1)->orderBy('id', 'desc')->get()->toArray();
            if (!empty($resume)) {
                $newarr = [];
                foreach ($resume as $key => $new) {
                    $pdf  = str_replace("public", env('APP_URL') . "public", $new['pdf']);
                    $data['id']             = $new['id'];
                    $data['user_id']        = $new['user_id'];
                    $data['city_id']        = $new['city_id'];
                    $data['status']        = $new['status'];
                    $data['name']           = $new['name'];
                    $data['education']      = $new['education'];
                    $data['job_experience'] = $new['job_experience'];
                    $data['expectation']    = $new['expectation'];
                    $data['pdf']            = $pdf;
                    $data['created_at']     = $new['created_at'];
                    $data['updated_at']     = $new['id'];

                    array_push($newarr, $data);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'List of Resume',
                    'data' => $newarr,
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'No record exist',
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
                        'name'           => ['required', 'string'],
                        'education'      => ['required'],
                        'job_experience' => ['required'],
                        'expectation'    => ['nullable', 'numeric'],
                        'pdf'            => ['nullable', 'mimes:pdf']
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['status' => false, 'message' => $validator->errors()]);
                    }

                    if ($file = $request->file('pdf')) {
                        $pdfname = md5(rand('1000', '10000'));
                        $extension = strtolower($file->getClientOriginalExtension());
                        $pdf_full_name = $pdfname . '.' . $extension;
                        $upload_path = 'public/resumePdf/';
                        $pdf_url = $upload_path . $pdf_full_name;
                        $file->move($upload_path, $pdf_full_name);
                    } else {
                        $pdf_url = null;
                    }
                    $resume = Resume::create([
                        'name' => $request->name,
                        'education' => $request->education,
                        'user_id' => $id,
                        'city_id' => $request->city_id ?? null,
                        'job_experience' => $request->job_experience,
                        'expectation' => $request->expectation,
                        'pdf' => $pdf_url,
                    ]);
                    $imp_pdf = str_replace("public", env('APP_URL') . "public", $pdf_url);

                    $resume['pdf'] = ($imp_pdf != "") ? $imp_pdf : "";

                    // $resume['pdf']            = $imp_pdf;
                    return response()->json([
                        'status' => true,
                        'message' => 'Added Successfully',
                        'data' => $resume,
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
                $resume = Resume::where('id', $id)->first();
                if (!empty($resume)) {
                    $validator = Validator::make($request->all(), [
                        'name'           => ['required', 'string'],
                        'education'      => ['required'],
                        'city_id'        => ['required', 'numeric'],
                        'job_experience' => ['required'],
                        'expectation'    => ['nullable', 'numeric'],
                        'pdf'            => ['nullable', 'mimes:pdf']
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['status' => false, 'message' => $validator->errors()]);
                    }
                    if ($file = $request->file('pdf')) {
                        $pdfname = md5(rand('1000', '10000'));
                        $extension = strtolower($file->getClientOriginalExtension());
                        $pdf_full_name = $pdfname . '.' . $extension;
                        $upload_path = 'public/resumePdf/';
                        $pdf_url = $upload_path . $pdf_full_name;
                        $file->move($upload_path, $pdf_full_name);
                    } else {
                        $pdf_url = null;
                    }

                    $imp_pdf = str_replace("public", env('APP_URL') . "public", $pdf_url);

                    $data['name']           = $request->name;
                    $data['education']      = $request->education;
                    $data['city_id']        = $request->city_id;
                    $data['job_experience'] = $request->job_experience;
                    $data['expectation']    = $request->expectation;
                    $data['pdf']            = $imp_pdf;

                    $updatedata = $resume->update($data);

                    $get = DB::table('resumes')->where('resumes.id', $id)->select('resumes.id', 'resumes.name', 'resumes.education', 'resumes.job_experience', 'resumes.expectation', 'resumes.pdf', 'cities.*')
                        ->join('cities', 'resumes.city_id', 'cities.id')->get();

                    $imp_image = str_replace("public", env('APP_URL') . "public", $pdf_url);

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

    public function resumeListOfUser()
    {
        try {
            $resume = Resume::orderBy('id', 'desc')->get()->toArray();
            if (!empty($resume)) {
                $newarr = [];
                foreach ($resume as $key => $new) {
                    $pdf  = str_replace("public", env('APP_URL') . "public", $new['pdf']);
                    $data['id']             = $new['id'];
                    $data['user_id']        = $new['user_id'];
                    $data['city_id']        = $new['city_id'];
                    $data['status']        = $new['status'];
                    $data['name']           = $new['name'];
                    $data['education']      = $new['education'];
                    $data['job_experience'] = $new['job_experience'];
                    $data['expectation']    = $new['expectation'];
                    $data['pdf']            = $pdf;
                    $data['created_at']     = $new['created_at'];
                    $data['updated_at']     = $new['id'];

                    array_push($newarr, $data);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'List of Resume',
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


    public function addResumeViaAdmin(Request $request)
    {
        ## add resume by admin
        $id = auth()->user()->id;
        try {
            if ($id == 1) {
                $validator = Validator::make($request->all(), [
                    'name'           => ['required', 'string'],
                    'education'      => ['required'],
                    'city_id'        => ['required', 'numeric'],
                    'job_experience' => ['required'],
                    'expectation'    => ['nullable', 'numeric'],
                    'pdf'            => ['nullable', 'mimes:pdf']
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()]);
                }

                $city = City::where('id', $request->city_id)->first();
                if (!empty($city)) {
                    if ($file = $request->file('pdf')) {
                        $pdfname = md5(rand('1000', '10000'));
                        $extension = strtolower($file->getClientOriginalExtension());
                        $pdf_full_name = $pdfname . '.' . $extension;
                        $upload_path = 'public/resumePdf/';
                        $pdf_url = $upload_path . $pdf_full_name;
                        $file->move($upload_path, $pdf_full_name);
                    } else {
                        $pdf_url = null;
                    }
                    $resume = Resume::create([
                        'name'           => $request->name,
                        'education'      => $request->education,
                        'city_id'        => $request->city_id,
                        'job_experience' => $request->job_experience,
                        'expectation'    => $request->expectation,
                        'pdf'            => $pdf_url,
                        'user_id'        => $id,
                        'status'         => 1,
                    ]);

                    $imp_pdf = str_replace("public", env('APP_URL') . "public", $pdf_url);

                    $resume['pdf'] = $imp_pdf;

                    return response()->json([
                        'status' => true,
                        'message' => 'Resume Added Successfully By Admin',
                        'data' => $resume,
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

    public function acceptResume(Request $request)
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
                $resume = Resume::where('id', $id)->first();
                if (!empty($resume)) {
                    $resumecity = Resume::where('id', $id)->where('city_id', '!=', null)->first();
                    if (!empty($resumecity)) {
                        if ($resume->status == 0) {
                            $resume->status = 1;
                            $updateStatus = $resume->update();
                            return response()->json([
                                'status' => true,
                                'message' => 'Request Accepted',
                                'data' => $resume,
                            ]);
                        } elseif ($resume->status == 1) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Request already accepted By Admin so you can not accept again',
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

    public function denyResume(Request $request)
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
                $resume = Resume::where('id', $id)->first();
                if (!empty($resume)) {
                    if ($resume->status == 0) {
                        $resume->status = 2;
                        $updateStatus = $resume->update();
                        return response()->json([
                            'status' => true,
                            'message' => 'Request Denied',
                            'data' => $resume,
                        ]);
                    } elseif ($resume->status == 2) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Request already denied By Admin so you can not deny again',
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

    public function showbResumeViacity(Request $request)
    {
        $city_id = $request->city_id;
        try {
            if ($city_id == null) {
                $resume = Resume::where('status', 1)->orderBy('id', 'desc')->get()->toArray();

                $newarr = [];
                foreach ($resume as $key => $new) {
                    $new['pdf'] = str_replace("public", env('APP_URL') . "public", $new['pdf']);

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
                    'message' => 'All resume list',
                    'data' => $newarr,
                ]);
            } else {
                $city = City::where('id', $city_id)->first();
                if (!empty($city)) {
                    $resume = Resume::where('resumes.city_id', $city_id)->where('status', 1)
                        ->select('resumes.*', 'users.name', 'cities.city_name')
                        ->join('users', 'users.id', 'resumes.user_id')
                        ->join('cities', 'cities.id', 'resumes.city_id')
                        ->orderBy('resumes.id', 'desc')
                        ->get();
                    if (!empty($resume)) {

                        $newarr = [];
                        foreach ($resume as $key => $new) {
                            $new['pdf'] = str_replace("public", env('APP_URL') . "public", $new['pdf']);

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
                            'message' => 'Resume list on the city basis',
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
