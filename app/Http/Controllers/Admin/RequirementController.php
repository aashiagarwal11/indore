<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use App\Models\Requirement;
use App\Models\City;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Watermark;
use Image;



class RequirementController extends Controller
{
    public function requirementList()
    {
        $apiurl = env('APP_URL') . 'api/requirementListOfUser';
        $response = Http::get($apiurl, [
            'role_id' => auth()->user()->role_id,
        ]);
        $newdata =  $response->json($key = null, $default = null);
        $birthdayData = $newdata['data'];
        return view('admin.Requirement.index', compact('birthdayData'));
    }


    public function requirementImage($id)
    {
        $bdata = Requirement::where('id', $id)->first();
        $bdata->image = str_replace("public", env('APP_URL') . "public", $bdata->image);


        $exp = explode('|', $bdata->image);
        return view('admin.Requirement.requirementImage', compact('exp', 'id'));
    }

    public function getrequirementForm()
    {
        $cityData = City::get();
        return view('admin.Requirement.requirementForm', compact('cityData'));
    }

    public function addrequirement(Request $request)
    {
        $validateImageData = $request->validate([
            'title'          => ['required', 'string'],
            'salary'         => ['nullable'],
            'city_id'        => ['required', 'numeric'],
            'working_time'   => ['nullable'],
            'comment'        => ['nullable'],
            // 'image'          => ['nullable', 'mimes:jpeg,png,jpg'],
            'image.*'        => ['nullable', 'mimes:jpeg,png,jpg']
        ]);

        $city = City::where('id', $request->city_id)->first();

        if (!empty($city)) {
            $images = array();
            if ($files = $request->file('image')) {
                foreach ($files as $file) {
                    $imgname = md5(rand('1000', '10000'));
                    $extension = strtolower($file->getClientOriginalExtension());
                    $img_full_name = $imgname . '.' . $extension;
                    $upload_path = 'public/requirement/';
                    $img_url = $upload_path . $img_full_name;

                    $wimage = Watermark::first();
                    // dd($wimage);

                    $waterMarkUrl = $wimage->image;
                    if (!empty($waterMarkUrl)) {
                        $imgFile = Image::make($file->getRealPath());
                        $imgFile->insert($waterMarkUrl, 'bottom-right', 5, 5, function ($font) {
                            $font->width(10);
                            $font->hright(2);
                        });
                        $imgFile->save($img_url);
                    }



                    // $file->move($upload_path, $img_full_name);
                    array_push($images, $img_url);
                }
            }
            $imp_image =  implode('|', $images);
            $birthday = Requirement::create([
                'title'          => $request->title,
                'salary'         => $request->salary,
                'city_id'        => $request->city_id,
                'working_time'   => $request->working_time,
                'comment'        => $request->comment,
                'status'         => 1,
                'image'          => $imp_image,
            ]);



            return redirect()->route('requirementList')->with('message', 'Added Successfully');
        }
    }

    public function getrequirementEditForm(Request $request, $id)
    {
        $bdata = Requirement::where('id', $id)->first();
        $cityData = City::get();

        return view('admin.Requirement.requirementEditForm', compact('bdata', 'cityData'));
    }

    public function updaterequirement(Request $request)
    {
        $id = $request->id;
        $birthday = Requirement::where('id', $id)->first();
        if (!empty($birthday)) {
            $validateImageData = $request->validate([
                'title'          => ['required'],
                'salary'         => ['nullable'],
                'city_id'        => ['required'],
                'working_time'   => ['nullable'],
                'comment'        => ['nullable'],
            ]);

            $data['title'] = $request->title;
            $data['salary'] = $request->salary;
            $data['city_id'] = $request->city_id;
            $data['working_time'] = $request->working_time;
            $data['comment'] = $request->comment;
            $updatedata = $birthday->update($data);
        }
        return redirect()->route('requirementList')->with('message', 'Update Successfully');
    }

    public function acceptrequirement(Request $request)
    {
        $apiurl = env('APP_URL') . 'api/acceptRequirement';
        $response = Http::post($apiurl, [
            'role_id' => auth()->user()->role_id,
            'id' => $request->id,
        ]);
        $newdata =  $response->json();
        $message = $newdata['message'];
        return redirect()->back()->with('message', $message);
    }

    public function denyrequirement(Request $request)
    {
        $apiurl = env('APP_URL') . 'api/denyRequirement';
        $response = Http::post($apiurl, [
            'role_id' => auth()->user()->role_id,
            'id' => $request->id,
        ]);
        $newdata =  $response->json();
        $message = $newdata['message'];
        return redirect()->back()->with('message', $message);
    }

    public function addrequirementImage(Request $request, $id)
    {

        $validateImageData = $request->validate([
            'image.*'       => ['nullable', 'mimes:jpeg,png,jpg'],
        ]);
        $birthday = Requirement::where('id', $id)->first();
        if (!empty($birthday)) {

            $exp = explode('|', $birthday->image);

            if ($files = $request->file('image')) {
                foreach ($files as $file) {
                    $imgname = md5(rand('1000', '10000'));
                    $extension = strtolower($file->getClientOriginalExtension());
                    $img_full_name = $imgname . '.' . $extension;
                    $upload_path = 'public/requirement/';
                    $img_url = $upload_path . $img_full_name;

                    $wimage = Watermark::first();
                    // dd($wimage);

                    $waterMarkUrl = $wimage->image;
                    if (!empty($waterMarkUrl)) {
                        $imgFile = Image::make($file->getRealPath());
                        $imgFile->insert($waterMarkUrl, 'bottom-right', 5, 5, function ($font) {
                            $font->width(10);
                            $font->hright(2);
                        });
                        $imgFile->save($img_url);
                    }


                    // $file->move($upload_path, $img_full_name);
                    array_push($exp, $img_url);
                }
            }

            $imp_image =  implode('|', $exp);

            $data['image'] = $imp_image;

            $updatedata = $birthday->update($data);
            return response()->json([
                'success' => true,
                'message' => 'Image added successfully',
            ]);
        }
    }


    public function deleterequirementImage(Request $request)
    {
        $get =  Requirement::where('id', $request->id)->first();
        $exp = explode('|', $get->image);
        unset($exp[$request->key]);
        $imp = implode('|', $exp);
        $get->image = $imp;
        $data = Requirement::where('id', $request->id)->update(['image' => $imp]);
        return response()->json(['data' => $data, 'message' => 'Deleted Successfully']);
    }
}
