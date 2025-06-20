<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    //

    public function index()
    {
        $book = new Book();

        return response()->json([
            'message' => 'Request sucessfully',
            'data' => $book::all()
        ]);
    }

    public function create(Request $request)
    {
        $book = Book::create([
            'title' => $request->title,
            'isbn' => $request->isbn,
            'author_name' => $request->author_name,
            'publication_year' => $request->publication_year,
            'number_of_copies' => $request->number_of_copies,
            'category' => $request->category
        ]);
        if ($book) {
            return response()->json([
                "message" => "Create Book successfuly!",
                "data" => $book
            ], 200);
        }
    }

    public function edit(Request $request, int $id)
    {
        $book = Book::where('id', $id)->update([
            'title' => $request->title,
            'isbn' => $request->isbn,
            'author_name' => $request->author_name,
            'publication_year' => $request->publication_year,
            'number_of_copies' => $request->number_of_copies,
            'category' => $request->category
        ]);
        if ($book) {
            return response()->json([
                "message" => "Edit Book successfuly!",
                "data" => $book
            ], 200);
        }
    }

    public function delete(int $id)
{
    $book = Book::where('id', $id)->delete();
    if ($book){
        return response()->json([
            "message" => "delete sccessfuly!",
            "data" => $book
        ]);
    }
}

public function show( $id)
{
    $book = Book::find($id);
    return response()->json([
        'message' => 'Request successful!',
        'data' => $book  
    ]);
} 
}
