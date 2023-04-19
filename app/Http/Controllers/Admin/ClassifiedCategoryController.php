<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use App\Models\KrishiMandiBhav;
use App\Models\Sale;
use Illuminate\Support\Facades\Validator;


class ClassifiedCategoryController extends Controller
{
    public function classifiedCategoryList()
    {
        // $apiurl = env('APP_URL') . 'api/sell';
        // $response = Http::get($apiurl, [
        //     'role_id' => auth()->user()->role_id,
        // ]);
        // $newdata =  $response->json($key = null, $default = null);
        // dd($newdata);
        // $birthdayData = $newdata['data'];
        // return view('admin.City.index', compact('birthdayData'));


        $birthdayData =  Sale::orderBy('id', 'desc')->get()->toArray();
        return view('admin.Category.index', compact('birthdayData'));
    }

    public function getclassifiedCategoryForm()
    {
        return view('admin.Category.categoryForm');
    }

    public function addclassifiedCategory(Request $request)
    {
        $validateImageData = $request->validate([
            'type'     => ['required']
        ]);

        $chkcityname = Sale::where('type', $request->type)->first();

        if (empty($chkcityname)) {
            $city = Sale::create([
                'type' => $request->type,
            ]);
            return redirect()->route('classifiedCategoryList')->with('message', 'Added Successfully');
        } else {
            return redirect()->route('getclassifiedCategoryForm')->with('message', 'Category Already Exist');
        }
    }

    public function getclassifiedCategoryEditForm(Request $request, $id)
    {
        $bdata = Sale::where('id', $id)->first();

        return view('admin.Category.categoryEditForm', compact('bdata'));
    }

    public function updateclassifiedCategory(Request $request)
    {
        $id = $request->id;

        $city = Sale::where('id', $id)->first();
        if (!empty($city)) {
            $validateImageData = $request->validate([
                'type'       => ['required'],
            ]);
            $chkcityname = Sale::where('type', $request->type)->where('id', '!=', $id)->first();

            if ($chkcityname) {
                return redirect()->back()->with('message', 'Category Already Exist');
            } else {
                $data['type'] = $request->type;
                $updatedata = $city->update($data);
                return redirect()->route('classifiedCategoryList')->with('message', 'Updated Successfully');
            }
        }
    }

    public function deleteclassifiedCategory($id)
    {
        $del = Sale::where('id', $id)->first();
        $del->delete();
        return redirect()->route('classifiedCategoryList')->with('message', 'Deleted Successfully');
    }
}
