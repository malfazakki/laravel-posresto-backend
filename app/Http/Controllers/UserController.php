<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller {
    // Index
    public function index(Request $request) {
        // Get all users with pagination
        $users = DB::table('users')
            ->when($request->input('name'), function ($query, $name) {
                $query->where('name', 'like', '%' . $name . '%')
                    ->orWhere('name', 'like', '%' . $name . '%');
            })
            ->paginate(10);

        return view('pages.users.index', compact('users'));
    }

    // Create
    public function create() {
        return view('pages.users.create');
    }

    // Store
    public function store(Request $request) {
        // Validate the request
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,staff,user',
            'password' => 'required|min:8',
        ]);

        // Store the request
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('users.index')->with('success', 'User has been created successfully');
    }

    // Show
    public function show() {
        return view('pages.users.show');
    }

    // Edit
    public function edit($id) {
        $user = User::findOrFail($id);
        return view('pages.users.edit', compact('user'));
    }

    // Update
    public function update(Request $request, $id) {
        // Validate the request
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'role' => 'required|in:admin,staff,user',
        ]);

        // Update the request
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();

        // If password is not empty
        if ($request->password) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return redirect()->route('users.index')->with('success', 'User has been Updated Successfully');
    }

    // Destroy
    public function destroy($id) {
        // Delete the request
        $user = User::find($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User has been Deleted Successfully');
    }
};
