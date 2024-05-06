<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\OTPVerificationServiceProvider;
use App\Providers\ValidationServiceProvider;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Handles the mobile number verification state for a user by sending an OTP if the user is not verified.
     *
     * This function checks if the user's mobile number is pending verification and, if so,
     * initiates the OTP verification process by sending an OTP to the user's mobile number.
     * It prepares a response indicating whether the OTP was successfully sent
     * and includes details for the next steps in the verification process.
     *
     * @param User $user_instance An instance of the user model whose mobile number is being verified.
     * @return JsonResponse Returns a JSON response with the verification state, including instructions for the next step,
     *                      whether the OTP was sent, and other relevant information.
     * @throws Exception
     */
    private function mobilePendingVerificationState(User $user_instance): JsonResponse
    {
        $otp_response = OTPVerificationServiceProvider::sendOTP(
            recipient: $user_instance->mobile
        );

        $response_data = [
            'next_step' => 'otp_verification',
            'otp_sent' => $otp_response["code"] == 200,
            'otp_hash' => $otp_response["hash"]
        ];

        if ($otp_response["code"] != 200) {
            $response_data['retry_after_seconds'] = $otp_response["retry_after_seconds"];
        }

        return response()->json([
            'message' => 'Mobile number is in pending verification state. Go to next step.',
            'data' => $response_data
        ]);
    }

    /**
     * Handles the user signup process including mobile number validation, user creation, and sending OTP for account verification.
     *
     * This function validates the request data for a new user signup, including mobile number and password.
     * It checks if the mobile number is already associated with an existing account.
     * If not, it validates the mobile number, creates a new user, assigns a default role to the user,
     * and initiates the OTP verification process. The function returns a JSON response
     * indicating the outcome of these operations, including the next steps for account verification or error messages.
     *
     * @param Request $request The request object containing signup information, specifically mobile number and password.
     * @return JsonResponse Returns a JSON response indicating the status of the signup process.
     *                      This includes success messages with next steps for verification or error messages with reasons.
     */
    public function signUp(Request $request): JsonResponse
    {
        try {
            $validator = ValidationServiceProvider::validateRequestData($request, [
                'mobile' => 'required|string|min:10|max:12',
                'password' => 'required|string|min:6'
            ]);

            if (!$validator['is_valid']) {
                return $validator['response'];
            }

            $validated_data = $validator['validated_data'];
            $mobile_number = $validated_data['mobile'];
            $password = $validated_data['password'];

            $isStrongPassword = ValidationServiceProvider::isPasswordStrong($password);
            if (!$isStrongPassword['strong']) {
                return response()->json([
                    'message' => 'You have entered an weak password.',
                    'errors' => $isStrongPassword['errors']
                ], 400);
            }

            $userExists = User::where('mobile', $mobile_number)->where('is_mobile_verified', true)->first();
            if ($userExists) {
                return response()->json([
                    'message' => 'Another user is already registered with this mobile number.'
                ], 400);
            } else {
                if (ValidationServiceProvider::isValidMobileNumber($mobile_number)) {
                    $user_instance = User::where('mobile', $mobile_number)->where('is_mobile_verified', false)->first();
                    if ($user_instance) {
                        $user_instance->mobile = $mobile_number;
                        $user_instance->password = $password;
                        $user_instance->save();

                        $re_init = true;
                    } else {
                        $user_instance = User::create([
                            'mobile' => $mobile_number,
                            'password' => $password
                        ]);
                        $user_instance->assignRole('User');

                        $re_init = false;
                    }

                    $otp_response = OTPVerificationServiceProvider::sendOTP(
                        recipient: $user_instance->mobile,
                        after_verification_step: 'sign_in',
                        re_init: $re_init
                    );

                    if ($otp_response["code"] == 200) {
                        return response()->json([
                            'message' => 'Signup has been completed successfully.',
                            'data' => [
                                "next_step" => "otp_verification",
                                "otp_hash" => $otp_response["hash"]
                            ]
                        ]);
                    } else {
                        return response()->json([
                            'message' => $otp_response["message"],
                            'data' => [
                                "otp_hash" => $otp_response["hash"],
                                'retry_after_seconds' => $otp_response["retry_after_seconds"]
                            ]
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'message' => 'Failed to verify mobile number.',
                        'data' => [
                            "mobile" => "The mobile number could not be verified. Please check the number and try again."
                        ]
                    ], 400);
                }
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Handles the user sign-in process including mobile number and password verification and returns an appropriate response.
     *
     * This function validates the sign-in request data for mobile number and password.
     * It checks if a user with the provided mobile number exists and verifies the password.
     * If the credentials are correct and the user's mobile number is verified,
     * it checks whether the user's account is active and optionally whether the profile needs completion.
     * On successful authentication, it generates and returns a JWT token for the session,
     * along with user details and the next recommended action for the user.
     *
     * @param Request $request The request object containing sign-in information, specifically mobile number and password.
     * @return JsonResponse Returns a JSON response with the authentication status. On successful sign-in,
     *                      it includes the JWT token, token type, expiration time, user details,
     *                      and the next step (e.g., 'home', 'update_profile').
     *                      On failure, it returns an appropriate error message.
     */
    public function signIn(Request $request): JsonResponse
    {
        try {
            $validator = ValidationServiceProvider::validateRequestData($request, [
                'mobile' => 'required|string',
                'password' => 'required|string|min:6'
            ]);

            if (!$validator['is_valid']) {
                return $validator['response'];
            }

            $validated_data = $validator['validated_data'];
            $mobile_number = $validated_data['mobile'];
            $password = $validated_data['password'];

            $user_instance = User::where('mobile', $mobile_number)->where('is_mobile_verified', true)->first();
            if ($user_instance) {
                if ($user_instance->is_mobile_verified) {
                    if (!$user_instance->is_active) {
                        return response()->json([
                            'message' => 'Your account is not active yet.'
                        ], 400);
                    }

                    $next_step = 'home';
                    if ($user_instance->first_name == null || $user_instance->first_name == "") {
                        $next_step = 'update_profile';
                    }

                    $credentials = ['mobile' => $mobile_number, 'password' => $password];

                    if (Auth::guard('api')->attempt($credentials)) {
                        $user = Auth::guard('api')->user();
                        $token = JWTAuth::fromUser($user);
                        return response()->json([
                            'message' => 'You have been authorized successfully.',
                            'data' => [
                                'next_step' => $next_step,
                                'access_token' => $token,
                                'token_type' => 'bearer',
                                'expires_in' => auth('api')->factory()->getTTL() * 60,
                                'user' => $user
                            ]
                        ]);
                    } else {
                        return response()->json([
                            'message' => 'You have entered an invalid credentials.'
                        ], 400);
                    }
                } else {
                    return $this->mobilePendingVerificationState($user_instance);
                }
            } else {
                return response()->json([
                    'message' => 'No account found with the provided mobile number.'
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Initiates the process for resetting a user's password by verifying their mobile number and sending an OTP.
     *
     * This function handles a request to reset a password by first validating the provided mobile number.
     * If the mobile number is associated with an existing and verified user account that is active,
     * it proceeds to send an OTP to the user's mobile number. This OTP is part of the verification process
     * required to reset the user's password. The response indicates the success of the OTP sending process
     * and provides details for the next steps. In cases where the user's mobile number is not verified
     * or the account is not active, appropriate responses are returned to guide the user accordingly.
     *
     * @param Request $request The request object containing the mobile number for which the password reset is requested.
     * @return JsonResponse Returns a JSON response with details of the OTP sending status for password reset,
     *                      including next steps, or error messages for issues like unverified mobile numbers,
     *                      inactive accounts, or non-existent user accounts.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $validator = ValidationServiceProvider::validateRequestData($request, [
                'mobile' => 'required|string|min:10|max:12'
            ]);

            if (!$validator['is_valid']) {
                return $validator['response'];
            }

            $validated_data = $validator['validated_data'];
            $mobile_number = $validated_data['mobile'];

            $user_instance = User::where('mobile', $mobile_number)->where('is_mobile_verified', true)->first();
            if ($user_instance) {
                if ($user_instance->is_mobile_verified) {
                    if (!$user_instance->is_active) {
                        return response()->json([
                            'message' => 'Your account is not active yet.'
                        ], 400);
                    }

                    $otp_response = OTPVerificationServiceProvider::sendOTP(
                        recipient: $user_instance->mobile,
                        forgot_password: true,
                        after_verification_step: 'reset_password'
                    );

                    if ($otp_response["code"] == 200) {
                        return response()->json([
                            'message' => 'Forget password OTP has been sent successfully.',
                            'data' => [
                                "next_step" => 'otp_verification',
                                "otp_hash" => $otp_response["hash"]
                            ]
                        ]);
                    } else {
                        return response()->json([
                            'message' => $otp_response["message"],
                            'data' => [
                                "otp_hash" => $otp_response["hash"],
                                'retry_after_seconds' => $otp_response["retry_after_seconds"]
                            ]
                        ], 400);
                    }
                } else {
                    return $this->mobilePendingVerificationState($user_instance);
                }
            } else {
                return response()->json([
                    'message' => 'No account found with the provided mobile number.'
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Handles the password reset process by verifying the OTP provided by the user and updating the user's password.
     *
     * This function validates the request data for a password reset, specifically the OTP hash and the new password.
     * It uses the OTP hash to verify if the OTP has been successfully verified for the purpose of resetting the password.
     * If the OTP is verified, it proceeds to update the user's password with the provided new password.
     * The function returns a response indicating the outcome of the password reset process,
     * including a success message with instructions for the next step or error messages for issues such as
     * failed OTP verification.
     *
     * @param Request $request The request object containing the OTP hash and the new password.
     * @return JsonResponse Returns a JSON response indicating the status of the password reset process.
     *                      On successful password reset, it includes instructions for the next step (e.g., 'sign_in').
     *                      On failure, it returns an appropriate error message.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $validator = ValidationServiceProvider::validateRequestData($request, [
                'otp_hash' => 'required|string',
                'password' => 'required|string|min:6'
            ]);

            if (!$validator['is_valid']) {
                return $validator['response'];
            }

            $validated_data = $validator['validated_data'];
            $otp_hash = $validated_data['otp_hash'];
            $password = $validated_data['password'];

            $otpVerificationResult = OTPVerificationServiceProvider::isOTPVerified(
                hash: $otp_hash,
                forgot_password: true
            );

            if ($otpVerificationResult['code'] == 200) {
                $user = User::where('id', $otpVerificationResult['user_id'])->firstOrFail();
                $user->password = $password;
                $user->save();

                return response()->json([
                    'message' => 'Your password has been reset successfully.',
                    'data' => [
                        "next_step" => 'sign_in'
                    ]
                ]);
            } else {
                return response()->json([
                    'message' => 'OTP verification pending. Please verify your OTP first.'
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Verifies an OTP (One Time Password) based on the provided OTP hash and OTP code from the request.
     *
     * This function is responsible for validating the OTP sent to the user's mobile during various verification processes
     * such as sign-up, password reset, etc. It validates the request data to ensure the presence of a valid OTP hash and
     * OTP code. Upon successful validation, it attempts to verify the OTP. If the OTP is correctly verified, it updates
     * the user's account status to reflect that the mobile number has been verified and, if applicable, activates the user's account.
     * The function returns a JSON response indicating whether the OTP verification was successful and provides details for
     * the next steps.
     *
     * @param Request $request The request object containing the OTP hash and OTP code for verification.
     * @return JsonResponse Returns a JSON response indicating the outcome of the OTP verification process.
     *                      On successful verification, it includes details for the next steps.
     *                      On failure, it returns an appropriate error message.
     */
    public function verifyOTP(Request $request): JsonResponse
    {
        try {
            $validator = ValidationServiceProvider::validateRequestData($request, [
                'otp_hash' => 'required|string',
                'otp_code' => 'required|string|min:4|max:4'
            ]);

            if (!$validator['is_valid']) {
                return $validator['response'];
            }

            $validated_data = $validator['validated_data'];
            $otp_hash = $validated_data['otp_hash'];
            $otp_code = $validated_data['otp_code'];

            $otp_verification_response = OTPVerificationServiceProvider::verifyOTP(
                hash: $otp_hash,
                otp: $otp_code
            );

            if ($otp_verification_response["code"] == 200) {
                User::where(['mobile' => $otp_verification_response['mobile'], 'is_mobile_verified' => false])->update(['is_active' => true, 'is_mobile_verified' => true, 'mobile_verified_at' => Carbon::now()]);
                return response()->json([
                    'message' => 'OTP has been verified successfully.',
                    'data' => [
                        "next_step" => $otp_verification_response['after_verification_step'],
                        "otp_verified" => true
                    ]
                ]);
            } else {
                return response()->json([
                    'message' => $otp_verification_response["message"]
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Handles the OTP resend request by validating the provided OTP hash and attempting to resend the OTP.
     *
     * This function validates the request data for an OTP hash and, upon successful validation,
     * attempts to resend the OTP associated with that hash. It caters to scenarios where the initial OTP may not
     * have been received by the user or when another OTP is requested. The function returns a JSON response indicating
     * the outcome of the OTP resend request, including a success message and details such as the next recommended action
     * and whether a new OTP was sent. In cases where the resend attempt is unsuccessful, it provides an appropriate error
     * message and guidance for the next steps.
     *
     * @param Request $request The request object containing the OTP hash for which the resend is requested.
     * @return JsonResponse Returns a JSON response with the status of the OTP resend attempt.
     *                      On success, it includes confirmation of the OTP resend, the updated OTP hash,
     *                      and instructions for the next step (typically 'otp_verification').
     *                      On failure, it returns an error message along with retry information.
     */
    public function resendOTP(Request $request): JsonResponse
    {
        try {
            $validator = ValidationServiceProvider::validateRequestData($request, [
                'otp_hash' => 'required|string'
            ]);

            if (!$validator['is_valid']) {
                return $validator['response'];
            }

            $validated_data = $validator['validated_data'];
            $otp_hash = $validated_data['otp_hash'];

            $otp_verification_response = OTPVerificationServiceProvider::resendOTP(
                hash: $otp_hash
            );

            if ($otp_verification_response["code"] == 200) {
                return response()->json([
                    'message' => "OTP has been resent successfully.",
                    'data' => [
                        'next_step' => 'otp_verification',
                        'otp_sent' => true,
                        'otp_hash' => $otp_verification_response["hash"],
                        'retry_after_seconds' => 0
                    ]
                ], 200);
            } else {
                $next_step = $otp_verification_response["verified"] ?? false ? 'sign_in' : 'otp_verification';
                $status_code = $next_step == "sign_in" ? 200 : 400;
                return response()->json([
                    'message' => $otp_verification_response["message"],
                    'data' => [
                        'next_step' => $next_step,
                        'otp_sent' => false,
                        'otp_hash' => $otp_verification_response["hash"],
                        'retry_after_seconds' => $otp_verification_response["retry_after_seconds"]
                    ]
                ], $status_code);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
