<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\AppFunctionProvider;
use App\Providers\ValidationServiceProvider;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Image;

class ProfileController extends Controller
{
    /**
     * Retrieves and returns the profile information of the currently authenticated user.
     *
     * This function accesses the currently authenticated user via the API guard and returns the user's profile information.
     * It is a straightforward method for fetching the authenticated user's data, assuming the user is successfully authenticated
     * and has a valid token. The function returns a JSON response containing the user's profile data.
     *
     * @param Request $request The incoming request object, used here primarily for authentication context.
     * @return JsonResponse Returns a JSON response with a success message and the authenticated user's profile data.
     */
    public function getUserProfile(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('api')->user();
            return response()->json([
                'message' => 'Profile information received successfully.',
                'data' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Updates the profile information of the currently authenticated user based on the provided request data.
     *
     * This function allows the currently authenticated user to update their profile information, such as first name,
     * last name, and email. It first validates the incoming request data to ensure that the provided values meet the
     * specified requirements. If validation fails, it returns the validation errors. Otherwise, it proceeds to update
     * the user's profile with the validated data and saves these changes to the database. The function then returns a
     * JSON response indicating successful update and includes the updated user profile data.
     *
     * @param Request $request The incoming request object containing the profile data to be updated.
     * @return JsonResponse Returns a JSON response with a success message and the updated profile data of the authenticated user.
     * @throws ValidationException
     */
    public function updateUserProfile(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('api')->user();

            $validator = ValidationServiceProvider::validateRequestData($request, [
                'first_name' => 'required|string|max:255',
                'last_name' => 'string|max:255|nullable',
                'email' => 'string|email|max:100|nullable',
                'address' => 'string|max:255|nullable',
                'city' => 'string|max:255|nullable',
                'post_code' => 'string|max:255|nullable',
                'country' => 'string|max:255|nullable',
                'profile_photo' => 'string|nullable',
            ]);

            if (!$validator['is_valid']) {
                return $validator['response'];
            }

            $validated_data = $validator['validated_data'];

            if (User::where('email', $validated_data['email'])->count() > 0) {
                $user_with_email = User::where('email', $validated_data['email'])->first();
                if ($user->id != $user_with_email->id) {
                    return response()->json([
                        'message' => __('This email address is already taken.')
                    ], 400);
                }
            }

            //            TODO: Email Verification in Required to Set/Change Email
            //
            //            if ($user->email != $validated_data['email']) {
            //                $user->is_email_verified = false;
            //                $user->email_verified_at = null;
            //            }
            //            TODO: Email Verification in Required to Set/Change Email

            $user->first_name = $validated_data['first_name'];
            $user->last_name = $validated_data['last_name'] ?? null;
            $user->email = $validated_data['email'] ?? null;
            $user->address = $validated_data['address'] ?? null;
            $user->city = $validated_data['city'] ?? null;
            $user->post_code = $validated_data['post_code'] ?? null;
            $user->country = $validated_data['country'] ?? null;

            if ($image = $request->input('profile_photo')) {
                $image = explode('base64,', $image);
                $extension = explode(';', explode('/', $image[0])[1])[0];
                $image = end($image);
                $image = str_replace(' ', '+', $image);
                $file_path = "uploads/user/" . Str::orderedUuid()->toString() . '.' . $extension;
                File::put($file_path, base64_decode($image));
                AppFunctionProvider::deleteFile($user->getRawOriginal('profile_photo'));
                $user->profile_photo = '/' . $file_path;
            }

            $user->save();

            return response()->json([
                'message' => 'Profile information updated successfully.',
                'data' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Deletes the profile of the currently authenticated user.
     *
     * This function facilitates the deletion of the user profile for the currently authenticated user via the API guard.
     * It performs a soft delete operation on the user's record, assuming that the underlying user model is set up to
     * support soft deletion. This action is irreversible through this API endpoint, meaning the user would need to
     * contact support for account restoration. The function returns a JSON response indicating the success of the
     * profile deletion process.
     *
     * @param Request $request The incoming request object, not directly used but necessary for the API endpoint.
     * @return JsonResponse Returns a JSON response with a message indicating successful deletion of the user profile.
     */
    public function deleteUserProfile(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('api')->user();
            $user->delete();

            return response()->json([
                'message' => 'User profile deleted successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }

    }

}
