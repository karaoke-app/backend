<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AuthRequest;
use App\Mail\VerifyMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Api\SocialAccountsService;
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
     * @param \App\Http\Controllers\Api\SocialAccountsService $accountService
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
            return response()->json(['error' => 'In order to login, you need to activate your account first.'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Registration
     * @param AuthRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function register(AuthRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        Mail::to($user->email)->send(new VerifyMail($user));

        return redirect()->route('login')
            ->with(['success' => 'Check your email to activate your account.']);;
    }

    /**
     * Account verification
     * @param User $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function activate($id)
    {
        $user = User::where('id', $id)->first();

        if (empty($user)) {
            return redirect()->to('/')
                ->with(['error' => 'Your activation link is either expired or invalid.']);
        }

        $user->is_activated = 1;
        $user->save();

        return redirect()->route('login')
            ->with(['success' => 'Congratulations! Your account is now activated.']);
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
