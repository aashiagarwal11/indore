<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use App\Models\City;
use App\Models\Resume;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class ResumeController extends Controller
{
    public function resumeList()
    {
        $apiurl = env('APP_URL') . 'api/resumeListOfUser';
        $response = Http::get($apiurl, [
            'role_id' => auth()->user()->role_id,
        ]);
        $newdata =  $response->json($key = null, $default = null);
        $birthdayData = $newdata['data'];
        return view('admin.Resume.index', compact('birthdayData'));
    }


    public function resumeImage($id)
    {
        $bdata = Resume::where('id', $id)->first();
        $bdata->pdf = str_replace("public", env('APP_URL') . "public", $bdata->pdf);
        // dd($bdata->pdf);


        // $exp = explode('|', $bdata->image);
        // return view('admin.Resume.resumeImage', compact('exp', 'id'));
    }

    public function getresumeForm()
    {
        $cityData = City::get();
        return view('admin.Resume.resumeForm', compact('cityData'));
    }

    public function addresume(Request $request)
    {
        $validateImageData = $request->validate([
            'name'           => ['required', 'string'],
            'education'      => ['required'],
            'city_id'        => ['required', 'numeric'],
            'job_experience' => ['required'],
            'expectation'    => ['nullable', 'numeric'],
            'pdf'            => ['nullable', 'mimes:pdf']
        ]);

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
                'status'         => 1,
            ]);

            return redirect()->route('resumeList')->with('message', 'Added Successfully');
        }
    }

    public function getresumeEditForm(Request $request, $id)
    {
        $bdata = Resume::where('id', $id)->first();
        $cityData = City::get();

        return view('admin.Resume.resumeEditForm', compact('bdata', 'cityData'));
    }

    public function updateresume(Request $request)
    {
        $id = $request->id;
        $birthday = Resume::where('id', $id)->first();
        if (!empty($birthday)) {
            $validateImageData = $request->validate([
                'name'           => ['required'],
                'education'      => ['required'],
                'city_id'        => ['required'],
                'job_experience' => ['required'],
            ]);

            $data['name'] = $request->name;
            $data['education'] = $request->education;
            $data['city_id'] = $request->city_id;
            $data['job_experience'] = $request->job_experience;
            $updatedata = $birthday->update($data);
        }
        return redirect()->route('resumeList')->with('message', 'Update Successfully');
    }

    public function acceptresume(Request $request)
    {
        $apiurl = env('APP_URL') . 'api/acceptResume';
        $response = Http::post($apiurl, [
            'role_id' => auth()->user()->role_id,
            'id' => $request->id,
        ]);
        $newdata =  $response->json();
        $message = $newdata['message'];
        return redirect()->back()->with('message', $message);
    }

    public function denyresume(Request $request)
    {
        $apiurl = env('APP_URL') . 'api/denyResume';
        $response = Http::post($apiurl, [
            'role_id' => auth()->user()->role_id,
            'id' => $request->id,
        ]);
        $newdata =  $response->json();
        $message = $newdata['message'];
        return redirect()->back()->with('message', $message);
    }

    // public function addresumeImage(Request $request, $id)
    // {
    //     $birthday = Resume::where('id', $id)->first();
    //     if (!empty($birthday)) {

    //         $exp = explode('|', $birthday->image);

    //         if ($files = $request->file('image')) {
    //             foreach ($files as $file) {
    //                 $imgname = md5(rand('1000', '10000'));
    //                 $extension = strtolower($file->getClientOriginalExtension());
    //                 $img_full_name = $imgname . '.' . $extension;
    //                 $upload_path = 'public/requirement/';
    //                 $img_url = $upload_path . $img_full_name;
    //                 $file->move($upload_path, $img_full_name);
    //                 array_push($exp, $img_url);
    //             }
    //         }

    //         $imp_image =  implode('|', $exp);

    //         $data['image'] = $imp_image;

    //         $updatedata = $birthday->update($data);
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Image added successfully',
    //         ]);
    //     }
    // }
}
