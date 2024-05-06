<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\OTPVerificationServiceProvider;
use App\Providers\ValidationServiceProvider;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Sign in the user.
     *
     * This method handles both the display of the sign-in form and the submission
     * of the sign-in form data. It validates the submitted data, authenticates
     * the user, and redirects accordingly.
     *
     * @param Request $request The HTTP request object.
     * @return View|RedirectResponse The sign-in view or a redirect response.
     */
    public function signIn(Request $request): View|RedirectResponse
    {
        try {
            if ($request->method() == 'POST') {
                $validator = ValidationServiceProvider::validateRequestData($request, [
                    'email' => 'required|string',
                    'password' => 'required|string',
                    'remember_me' => 'bool'
                ], web: true);

                if (!$validator['is_valid']) {
                    return redirect()->back()
                        ->with('error', $validator['response'])
                        ->withInput($request->input());
                }

                $validated_data = $validator['validated_data'];
                $email_address = $validated_data['email'];
                $password = $validated_data['password'];
                $remember_me = isset($validated_data['remember_me']) && boolval($validated_data['remember_me']);

                $user_instance = User::where('email', $email_address)->take(1)->first();
                $is_login_allowed = $user_instance->roles->where('is_admin_login_allowed', true)->count();
                $is_role_active = $user_instance->roles->where('is_active', true)->count();
                if ($user_instance && $is_login_allowed && $is_role_active) {
                    if (!$user_instance->is_active) {
                        return redirect()->back()
                            ->with('error', __('Your account is not active yet.'))
                            ->withInput($request->input());
                    }

                    $credentials = ['email' => $email_address, 'password' => $password];

                    if (Auth::attempt($credentials, $remember_me)) {
                        return redirect()->route('/');
                    } else {
                        return redirect()->back()
                            ->with('error', __('You have entered an invalid credentials.'))
                            ->withInput($request->input());
                    }
                } else {
                    return redirect()->back()
                        ->with('error', __('No account found with the provided email address.'))
                        ->withInput($request->input());
                }
            }
            return view('authentication.signin');
        } catch (Exception $e) {
            return redirect()->route('auth.signin')->with('error', $e->getMessage());
        }
    }

    /**
     * Sign out the authenticated user.
     *
     * This method logs out the authenticated user and redirects to the home page
     * with a success message if the logout is successful. If an error occurs during
     * the logout process, it redirects to the sign-in page with an error message.
     *
     * @return RedirectResponse A redirect response to the home page or sign-in page.
     */
    public function signOut(): RedirectResponse
    {
        try {
            Auth::logout();
            return redirect()->route('auth.signin')->with('success', __('You have been logged out successfully.'));
        } catch (Exception $e) {
            return redirect()->route('auth.signin')->with('error', $e->getMessage());
        }
    }

}
