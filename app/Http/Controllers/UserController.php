<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Auth;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $data = User::latest()->get();

        return view('users.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $roles = Role::pluck('username', 'username')->all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required',
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $user = User::find($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $user = User::find($id);

        if (!$user) {
            // Handle case where user with the given ID is not found
            return abort(404);
        }

        // Assuming you have a 'roles' relationship defined in your User model
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required',
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);

        // Get the user from the database
        $user = User::find($id);

        // Update user details
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');

        // Hash the password if it's present in the input
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Save the changes to the user
        $user->save();

        // Sync user roles
        $user->syncRoles([$request->input('roles')]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {
        $user = User::find($id);

        if ($user) {           
            $user->delete();

            return redirect()->route('admin')->with('success', [
                'message' => 'User deleted successfully!',
                'duration' => 3000,
            ]);
        }

        return redirect()->route('admin')->with('error', [
            'message' => 'Error deleting user!',
            'duration' => 3000,
        ]);
    }



    public function account_admin()
    {
        $user = Auth::user();
        return view('account_admindash', compact('user'));
    }

    public function account_user()
    {
        $user = Auth::user();
        return view('account_userdash', compact('user'));
    }
    public function updateadminProfile(Request $request)
    {
        $user = Auth::user();

        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
        ]);

        return redirect()->route('account_admindash')->with('success', 'Admin Profile updated successfully');
    }

    public function updateuserProfile(Request $request)
    {
        $user = Auth::user();

        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
        ]);

        return redirect()->route('account_userdash')->with('success', 'User Profile updated successfully');
    }
}
