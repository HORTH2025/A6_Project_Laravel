<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    //

    public function index()
    {
        $author = new Author();
        return response()->json([
            'message' => "request successfuly !",
            'data' => $author::all()
        ]);
    }

    public function create(Request $request)
{
    $author = Author::create([
        'name' => $request->name,
        'nationality' => $request->nationality,
        'writen_book' => $request->writen_book
    ]);

    if ($author) {
        return response()->json([
            'message' => "Create author successfully!",
            'data' => $author
        ]);
    } else {
        return response()->json([
            'message' => "Failed to create author."
        ]);
    }
}


    public function edit(Request $request, int $id)
    {
        $author = Author::where('id', $id)->update([
            'name' => $request->name,
            'nationality' => $request->nationality,
            'writen_book' => $request->writen_book
        ]);

        if ($author) {
            $updatedAuthor = Author::find($id);

            return response()->json([
                'message' => "Edit author successfully!",
                'data' => $updatedAuthor
            ], 200);
        } else {
            return response()->json([
                'message' => "Failed to update author."
            ], 400);
        }
    }


    public function delete(int $id)
    {
        $author = Author::where('id', $id)->delete();
        if ($author) {
            return response()->json([
                "message" => "delete sccessfuly!",
                "data" => $author
            ]);
        }
    }

    public function show($id)
    {
        $author = Author::find($id);
        return response()->json([
            'message' => 'Request successful!',
            'data' => $author
        ]);
    }
}
