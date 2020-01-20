<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Requests\Reactivation;
use App\Mail\VerifyMail;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Services\SocialAccountsService;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from SocialMedia.
     *
     * @param SocialAccountsService $accountService
     * @param $provider
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleProviderCallback(SocialAccountsService $accountService, $provider)
    {
        $user = Socialite::with($provider)->user();

        $authUser = $accountService->findOrCreate(
            $user,
            $provider
        );

        $token = JWTAuth::fromUser($authUser);

        return $this->respondWithToken($token);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid email or password'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        if(User::where('email', $request->email)->pluck('is_activated')->first() == 0)
        {
            return response()->json(['error' => 'Your account is deactivated'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Registration
     * @param AuthRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function register(AuthRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->activation_token = Str::random(40);

        $user->save();

        Mail::to($user->email)->send(new VerifyMail($user));

        return response()->json(['success' => true]);
    }

    /**
     * Account verification
     * @param User $id
     * @param User $activation_token
     * @return \Illuminate\Http\RedirectResponse
     */

    public function activate($id, $activation_token)
    {
        $user = User::where('id', $id)->first();

        if (!$user) {
            return redirect()->to('/')
                ->with(['error' => 'User does not exist']);
        }

        if ($user->activation_token != $activation_token) {
            return redirect()->to('/')
                ->with(['error' => 'Invalid token.']);
        }

        $user->is_activated = 1;
        $user->save();

        return redirect()->route('login')
            ->with(['success' => 'Congratulations! Your account is now activated.']);
    }

    /**
     * Account reactivation
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function reactivate(Reactivation $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user with given email cannot be found.'
            ], 400);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, password does not match with the registered one.'
            ], 400);
        }

        if($user->is_activated == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is already activated. Try to login.'
            ], 400);
        }

        $user->is_activated = 1;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Your account is reactivated. You can login now.'
        ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ], 201);
    }
}
