<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = Member::all();
        return response()->json([
            'message' => 'Request successful',
            'data' => $members
        ], 200);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $members = Member::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'dob' => $request->dob,
        ]);

        if ($members) {
            return response()->json([
                'message' => 'Member create successfully',
                'data' => $members
            ], 201);
        }
        return response()->json([
            'message' => 'create not found'
        ], 500);
    }



    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $members = Member::where('id', $id)->first();

        if ($members) {
            return response()->json([
                'message' => 'member found',
                'data' => $members
            ], 200);
        }
        return response()->json([
            'message' => 'member not found'
        ], 500);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $updated = Member::where('id', $id)->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'dob' => $request->dob
        ]);

        if ($updated) {
            // Fetch the updated member
            $member = Member::find($id);

            return response()->json([
                'message' => 'Member updated successfully',
                'data' => $member
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to update member or member not found'
        ], 500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(int $id)
    {
        $members = Member::where('id', $id)->delete();

        if ($members) {
            return response()->json([
                'message' => 'Member deleted successfully',
                'deleted' => $members
            ], 200);
        }

        return response()->json([
            'message' => 'Member not found'
        ], 404);
    }
}
