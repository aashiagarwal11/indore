<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use App\Models\Birthday;
use App\Models\City;
use App\Models\Sale;
use App\Models\SaleSubCategory;
use App\Models\SaleSubCategoryProduct;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class SaleController extends Controller
{
    public function saleList()
    {
        $apiurl = env('APP_URL') . 'api/sellFormListOfUser';
        $response = Http::get($apiurl, [
            'role_id' => auth()->user()->role_id,
        ]);
        $newdata =  $response->json($key = null, $default = null);
        $birthdayData = $newdata['data'];
        return view('admin.Sale.index', compact('birthdayData'));
    }

    public function saleImage($id)
    {
        $bdata = Birthday::where('id', $id)->first();
        $bdata->image = str_replace("public", env('APP_URL') . "public", $bdata->image);


        $exp = explode('|', $bdata->image);
        // $key = array_search("", $exp);
        // unset($exp[$key]);
        return view('admin.Birthday.birthdayImage', compact('exp', 'id'));
    }

    public function getsaleForm()
    {
        // $cityData = City::get();
        $category = Sale::get()->toArray();
        $subcategory = SaleSubCategory::get();
        return view('admin.Sale.saleForm', compact('category'));
    }

    public function getsaleFormajax(Request $request)
    {
        // dd($request->all());
        $data = SaleSubCategory::where('sale_id', $request->catid)->get()->toArray();
        // dd($data);
        if (!empty($data)) {
            $cityData = City::get();
            return response()->json(['data' => $data, 'cityData' => $cityData]);
        }

        return response()->json(['data' => null]);
    }

    public function addsale(Request $request)
    {
        // dd($request->all());


        // dd($request->all());

        $city = City::where('id', $request->city_id)->first();
        $chksellid = Sale::where('id', $request->sale_id)->first();


        if (!empty($city)) {
            $validateImageData = $request->validate([
                'sale_id'           => ['required'],
                'sub_cat_id'        => ['required'],
                'city_id'           => ['required'],
                'vendor_name'       => ['required', 'string', 'max:50'],
                'owner_or_broker'   => ['nullable', 'string', 'max:255'],
                'property_location' => ['nullable', 'string', 'max:255'],
                'price'             => ['nullable'],
                'image.*'           => ['nullable', 'mimes:jpeg,png,jpg'],
                'whatsapp_no'       => ['nullable', 'numeric', 'digits:10'],
                'call_no'           => ['nullable'],
            ]);


            if (!empty($chksellid->type)) {
                $images = array();
                if ($files = $request->file('image')) {
                    foreach ($files as $file) {
                        $imgname = md5(rand('1000', '10000'));
                        $extension = strtolower($file->getClientOriginalExtension());
                        $img_full_name = $imgname . '.' . $extension;
                        $upload_path = 'public/sellImage/';
                        $img_url = $upload_path . $img_full_name;
                        $file->move($upload_path, $img_full_name);
                        array_push($images, $img_url);
                    }
                }
                $imp_image =  implode('|', $images);

                if ($chksellid->type == 'vehicle') {
                    $validateImageData = $request->validate([
                        'vehicle_sighting'  => ['nullable', 'string', 'max:255'],
                        'brand'             => ['nullable', 'string', 'max:30'],
                        'model_name'        => ['nullable', 'string', 'max:20'],
                        'model_year'        => ['nullable', 'numeric'],
                        'fuel_type'         => ['nullable', 'string', 'max:20'],
                        'seater'            => ['nullable', 'numeric', 'max:30'],
                        'kilometer_running' => ['nullable', 'string', 'max:30'],
                        'insurance_period'  => ['nullable', 'string', 'max:20'],
                        'color'             => ['nullable', 'string', 'max:20'],
                    ]);
                    $sale = SaleSubCategoryProduct::create([
                        'sale_id'           => $request->sale_id,
                        'sub_cat_id'        => $request->sub_cat_id,
                        'vendor_name'       => $request->vendor_name,
                        'owner_or_broker'   => $request->owner_or_broker,
                        'property_location' => $request->property_location,
                        'price'             => $request->price,
                        'vehicle_sighting'  => $request->vehicle_sighting,
                        'brand'             => $request->brand,
                        'model_name'        => $request->model_name,
                        'model_year'        => $request->model_year,
                        'fuel_type'         => $request->fuel_type,
                        'seater'            => $request->seater,
                        'kilometer_running' => $request->kilometer_running,
                        'insurance_period'  => $request->insurance_period,
                        'color'             => $request->color,
                        'other_information' => $request->other_information ?? null,
                        'image'             => $imp_image,
                        'city_id'           => $request->city_id,
                        'user_id'           => 1,
                        'status'            => 1,
                        'whatsapp_no'       => $request->whatsapp_no,
                        'call_no'           => $request->call_no,
                    ]);
                } elseif ($chksellid->type == 'property') {
                    // dd($request->all());
                    $validateImageData = $request->validate([
                        'size_length_width'  => ['nullable'],
                        'room_qty'           => ['nullable'],
                        'kitchen'            => ['nullable'],
                        'hall'               => ['nullable'],
                        'lat_bath'           => ['nullable'],
                    ]);

                    $sale = SaleSubCategoryProduct::create([
                        'sale_id'           => $request->sale_id,
                        'sub_cat_id'        => $request->sub_cat_id,
                        'vendor_name'       => $request->vendor_name,
                        'owner_or_broker'   => $request->owner_or_broker,
                        'property_location' => $request->property_location,
                        'price'             => $request->price,
                        'size_length_width' => $request->size_length_width,
                        'other_information' => $request->other_information ?? null,
                        'image'             => $imp_image,
                        'city_id'           => $request->city_id,
                        'user_id'           => 1,
                        'status'            => 1,
                        'whatsapp_no'       => $request->whatsapp_no,
                        'call_no'           => $request->call_no,
                        'room_qty'          => $request->room_qty,
                        'kitchen'           => $request->kitchen,
                        'hall'              => $request->hall,
                        'lat_bath'          => $request->lat_bath,
                    ]);
                }
                // dd($sale);
                return response()->json($sale);
            }

            // return redirect()->route('birthdayList')->with('message', 'Added Successfully');
        }
    }

    public function getsaleEditForm(Request $request, $id)
    {
        $bdata = Birthday::where('id', $id)->first();
        $cityData = City::get();

        return view('admin.Birthday.birthdayEditForm', compact('bdata', 'cityData'));
    }

    public function updatesale(Request $request)
    {
        $id = $request->id;
        $birthday = Birthday::where('id', $id)->first();
        if (!empty($birthday)) {
            $validateImageData = $request->validate([
                'title'       => ['required'],
                'description' => ['required'],
                'city_id'     => ['required'],
                'video_url'   => ['nullable'],
            ]);

            $data['title'] = $request->title;
            $data['description'] = $request->description;
            $data['city_id'] = $request->city_id;
            $data['video_url'] = $request->video_url ?? null;
            $updatedata = $birthday->update($data);
        }

        // $apiurl = env('APP_URL') . 'api/birthday/' . $request->id;
        // $response = Http::put($apiurl, [
        //     'role_id' => auth()->user()->role_id,
        //     'title' => $request->title,
        //     'description' => $request->description,
        //     'city_id' => $request->city_id,
        //     'image' => $request->image,
        //     'video_url' => $request->video_url,
        // ]);
        // $newdata =  $response->json();
        return redirect()->route('birthdayList')->with('message', 'Update Successfully');
    }

    public function acceptsale(Request $request)
    {
        $apiurl = env('APP_URL') . 'api/acceptBirthday';
        $response = Http::post($apiurl, [
            'role_id' => auth()->user()->role_id,
            'id' => $request->id,
        ]);
        $newdata =  $response->json();
        $message = $newdata['message'];
        return redirect()->back()->with('message', $message);
    }

    public function denysale(Request $request)
    {
        $apiurl = env('APP_URL') . 'api/denyBirthday';
        $response = Http::post($apiurl, [
            'role_id' => auth()->user()->role_id,
            'id' => $request->id,
        ]);
        $newdata =  $response->json();
        $message = $newdata['message'];
        return redirect()->back()->with('message', $message);
    }

    public function addsaleImage(Request $request, $id)
    {
        $validateImageData = $request->validate([
            'image.*'       => ['nullable', 'mimes:jpeg,png,jpg'],
        ]);
        $birthday = Birthday::where('id', $id)->first();
        if (!empty($birthday)) {

            $exp = explode('|', $birthday->image);

            if ($files = $request->file('image')) {
                foreach ($files as $file) {
                    $imgname = md5(rand('1000', '10000'));
                    $extension = strtolower($file->getClientOriginalExtension());
                    $img_full_name = $imgname . '.' . $extension;
                    $upload_path = 'public/birthday/';
                    $img_url = $upload_path . $img_full_name;
                    $file->move($upload_path, $img_full_name);
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

    public function deletesaleImage(Request $request)
    {
        $get =  Birthday::where('id', $request->id)->first();
        $exp = explode('|', $get->image);
        unset($exp[$request->key]);
        $imp = implode('|', $exp);
        $get->image = $imp;
        $data = Birthday::where('id', $request->id)->update(['image' => $imp]);
        return response()->json(['data' => $data, 'message' => 'Deleted Successfully']);
    }
}
