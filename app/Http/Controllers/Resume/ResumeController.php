<?php

namespace App\Http\Controllers\Resume;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resume;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


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
                    'message' => 'List of Resume',
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
                        'name'           => ['required', 'string'],
                        'education'      => ['required'],
                        'job_experience' => ['required'],
                        'expectation'    => ['required', 'numeric'],
                        'pdf'          => ['required', 'mimes:pdf']
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['message' => $validator->errors()]);
                    }

                    if ($file = $request->file('pdf')) {
                        $pdfname = md5(rand('1000', '10000'));
                        $extension = strtolower($file->getClientOriginalExtension());
                        $pdf_full_name = $pdfname . '.' . $extension;
                        $upload_path = 'public/resumePdf/';
                        $pdf_url = $upload_path . $pdf_full_name;
                        $file->move($upload_path, $pdf_full_name);
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

                    $data['name']           = $resume->name;
                    $data['education']      = $resume->education;
                    $data['job_experience'] = $resume->job_experience;
                    $data['expectation']    = $resume->expectation;
                    $data['pdf']            = $imp_pdf;
                    $data['created_at']     = $resume->created_at;
                    $data['updated_at']     = $resume->updated_at;
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
                $validator = Validator::make($request->all(), [
                    'name'           => ['required', 'string'],
                    'education'      => ['required'],
                    'city_id'        => ['required', 'numeric'],
                    'job_experience' => ['required'],
                    'expectation'    => ['required', 'numeric'],
                    'pdf'          => ['required', 'mimes:pdf']
                ]);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()]);
                }

                $resume = Resume::where('id', $id)->first();
                if (!empty($resume)) {
                    if ($file = $request->file('pdf')) {
                        $pdfname = md5(rand('1000', '10000'));
                        $extension = strtolower($file->getClientOriginalExtension());
                        $pdf_full_name = $pdfname . '.' . $extension;
                        $upload_path = 'public/resumePdf/';
                        $pdf_url = $upload_path . $pdf_full_name;
                        $file->move($upload_path, $pdf_full_name);
                    }

                    $data['name']           = $request->title;
                    $data['education']      = $request->description;
                    $data['city_id']        = $request->city_id;
                    $data['job_experience'] = $request->city_id;
                    $data['expectation']    = $request->city_id;
                    $data['pdf']            = $pdf_url;
                    $updatedata = $resume->update($data);

                    $get = DB::table('resumes')->where('resumes.id', $id)->select('resumes.id', 'resumes.name', 'resumes.education', 'resumes.job_experience', 'resumes.expectation', 'resumes.pdf', 'cities.*')
                        ->join('cities', 'resumes.city_id', 'cities.id')->get();

                    $imp_image = str_replace("public", env('APP_URL') . "public", $pdf_url);

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
                    'message' => 'List of Resume',
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
}
