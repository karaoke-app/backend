<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        $users = User::get(['id', 'name', 'email'])->toArray();

        return $users;
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user with id ' . $id . ' cannot be found.'
            ], 400);
        }

        return $user;
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user with id ' . $id . ' cannot be found.'
            ], 400);
        }

        if ($user->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User could not be deleted.'
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'current_password' => 'required',
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required', 'same:new_password'],
        ]);

        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            return response()->json(['errors' => ['current'=> ['Current password does not match']]], 422);
        }

        if(strcmp($request->get('current_password'), $request->get('new_password')) == 0){
            return response()->json(['errors' => ['current'=> ['New password cannot be same as your current password']]], 422);
        }

        $user = Auth::user();
        $user->password = Hash::make($request->get('new_password'));
        $user->save();

        return response([
            'message' => 'Your password has been updated successfully.'
        ]);
    }

    public function changeUsername(Request $request)
    {
        $this->validate($request, [
            'current_username' => ['required'],
            'new_username' => ['required', 'string', 'max:255'],
            'new_username_confirmation' => ['required', 'same:new_username'],
        ]);

        if ($request->get('current_username') != Auth::user()->name){
            return response()->json(['errors' => ['current'=> ['Current username does not match']]], 422);
        }

        if(strcmp($request->get('current_username'), $request->get('new_username')) == 0){
            return response()->json(['errors' => ['current'=> ['New username cannot be same as your current username']]], 422);
        }

        $user = Auth::user();
        $user->name = $request->get('new_username');
        $user->save();

        return response([
            'message' => 'Your username has been updated successfully.'
        ]);
    }

    public function deactivation(Request $request)
    {
        $this->validate($request, [
            'password' => 'required',
            'password_confirmation' => ['required', 'same:password'],
        ]);

        if (!(Hash::check($request->get('password'), Auth::user()->password))) {
            return response()->json(['errors' => ['current'=> ['Current password does not match']]], 422);
        }

        if(Auth::user()->is_activated == 0){
            return response()->json(['errors' => ['activated'=> ['Your account is already deactivated']]], 422);
        }

        $user = Auth::user();

        $user->is_activated = 0;
        $user->save();

        return response([
            'message' => 'Your account has been deactivated successfully.'
        ]);
    }
}
