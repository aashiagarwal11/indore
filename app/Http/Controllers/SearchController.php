<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class SearchController extends Controller
{
    public function searchWordFromWholeDatabase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }



        try {
            $searchTerm = $request->search; // The search term you want to use
            // Search for keyword "laravel" in both users and posts tables
            $results = DB::select(
                DB::raw(
                    "(SELECT 'users' as type, name, email, NULL as title, NULL as description, NULL as image FROM users WHERE name LIKE '%laravel%') 
        UNION 
        (SELECT 'news' as type, title, description,image, NULL as name, NULL as email FROM posts WHERE title LIKE '%laravel%')"
                )
            );
            if (!empty($results)) {
                return response()->json([
                    'message' => 'Result on the basis of search term',
                    'data' => $results,
                ]);
            } else {
                return response()->json([
                    'message' => 'Empty',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }


    // public function searchWordFromWholeDatabase(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'search' => ['required'],
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['message' => $validator->errors()]);
    //     }



    //     try {
    //         $searchTerm = $request->search; // The search term you want to use
    //         $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames(); // Get a list of all tables in the database
    //         $results = collect(); // Initialize an empty collection to store the search results
    //         foreach ($tables as $table) { // Loop through all tables
    //             $columns = DB::getSchemaBuilder()->getColumnListing($table); // Get a list of all columns in the table
    //             foreach ($columns as $column) { // Loop through all columns in the table
    //                 $query = DB::table($table)
    //                     ->where($column, 'LIKE', '%' . $searchTerm . '%')
    //                     ->get(); // Execute the query to search for the search term in the current column
    //                 $results = $results->merge($query); // Add the query results to the collection of search results
    //             }
    //         }
    //         if (!empty($results)) {
    //             return response()->json([
    //                 'message' => 'Result on the basis of search term',
    //                 'data' => $results,
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'message' => 'Empty',
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => $e->getMessage(),
    //         ]);
    //     }
    // }
}
