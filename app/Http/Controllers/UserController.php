<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePassword;
use App\Http\Requests\ChangeUsername;
use App\Http\Requests\Deactivation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     *
     */
    public function index()
    {
        $users = User::get(['id', 'name', 'email'])->toArray();

        return $users;
    }

    /**
     * Display specified user.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user with id ' . $id . ' cannot be found.',
            ], 400);
        }

        return $user;
    }

    /**
     * Delete specified user (admin).
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user with id ' . $id . ' cannot be found.',
            ], 400);
        }

        if ($user->delete()) {
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User could not be deleted.',
            ], 500);
        }
    }

    /**
     * Delete profile (user).
     * @param $name
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($name)
    {
        $user = Auth::user();

        if (Auth::user()->name != $name) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, an error occurred.',
            ], 500);
        }

        if ($user->delete()) {
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User could not be deleted.',
            ], 500);
        }
    }

    /**
     * Changing password.
     * @param ChangePassword $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePassword $request)
    {
        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            return response()->json(['errors' => ['current' => ['Current password does not match']]], 422);
        }

        if (strcmp($request->get('current_password'), $request->get('new_password')) == 0) {
            return response()->json(['errors' => [
                'current' => [
                    'New password cannot be same as your current password',
                ],
            ]], 422);
        }

        $user = Auth::user();
        $user->password = Hash::make($request->get('new_password'));
        $user->save();

        return response()->json([
            'message' => 'Your password has been updated successfully.',
        ]);
    }

    /**
     * Changing username.
     * @param ChangeUsername $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function changeUsername(ChangeUsername $request)
    {
        if ($request->get('current_username') != Auth::user()->name) {
            return response()->json(['errors' => ['current' =>
                ['Current username does not match'],
            ]], 422);
        }

        if (strcmp($request->get('current_username'), $request->get('new_username')) == 0) {
            return response()->json(['errors' => ['current' =>
                ['New username cannot be same as your current username'],
            ]], 422);
        }

        $user = Auth::user();
        $user->name = $request->get('new_username');
        $user->save();

        return response([
            'message' => 'Your username has been updated successfully.',
        ]);
    }

    /**
     * Account deactivation.
     * @param Deactivation $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function deactivation(Deactivation $request)
    {
        if (!(Hash::check($request->get('password'), Auth::user()->password))) {
            return response()->json(['errors' => ['current' => ['Current password does not match']]], 422);
        }

        if (Auth::user()->is_activated == 0) {
            return response()->json(['errors' => ['activated' => ['Your account is already deactivated']]], 422);
        }

        $user = Auth::user();

        $user->is_activated = 0;
        $user->save();

        return response([
            'message' => 'Your account has been deactivated successfully.',
        ]);
    }
}
