<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidationServiceProvider
{
    /**
     * Validates a Sri Lankan mobile number.
     *
     * Sri Lankan mobile numbers should start with '07' followed by 8 digits when not including the country code.
     * When including the country code (+94), the number should then start with '94' followed by 9 digits,
     * with the initial '0' dropped.
     *
     * @param string $mobile_number The mobile number to validate.
     * @return bool Returns true if the mobile number is valid, false otherwise.
     */
    public static function isValidMobileNumber(string $mobile_number): bool
    {
        // Remove any whitespace or special characters that might be present
        $mobile_number = preg_replace('/\D/', '', $mobile_number);

        // Check if the mobile number starts with the local prefix '07' and is 10 digits long
        if (preg_match('/^07\d{8}$/', $mobile_number)) {
            return true;
        }

        // Check if the mobile number includes the country code '+94', drops the leading '0', and is 11 digits long
        // Note: Here '94' is followed by 9 digits making it 11 digits as '+94' is considered as 2 characters
        if (preg_match('/^94\d{9}$/', $mobile_number)) {
            return true;
        }

        // If none of the above conditions are met, the number is invalid
        return false;
    }

    /**
     * Get the first validation error message from an array of errors.
     *
     * This function retrieves the first error message from an array of validation errors.
     *
     * @param array $errors The array of validation errors.
     * @return string|null The first validation error message, or null if no errors found.
     */
    public static function getValidationMessage(array $errors): ?string
    {
        foreach ($errors as $key => $value) {
            if (count($value) > 0) {
                return $value[0];
            }
        }
        return null;
    }

    /**
     * Validate request data against given validation rules.
     *
     * This function validates the data from a request object based on specified validation rules.
     * If validation fails, it returns an error response with validation errors.
     *
     * @param mixed $request The request object containing data to validate.
     * @param array $validation_rules The validation rules to apply.
     * @return array An array containing validation results including whether the data is valid,
     *               any error response, and the validated data.
     * @throws ValidationException
     */
    public static function validateRequestData($request, array $validation_rules, bool $web = false): array
    {
        $validator = Validator::make($request->all(), $validation_rules);

        $response = [];
        $is_valid = true;

        if ($validator->fails()) {
            if(!$web){
                $response = response()->json([
                    'message' => __('Data validation failed.'),
                    'errors' => $validator->errors()->toArray()
                ], 400);
            } else {
                $response = ValidationServiceProvider::getValidationMessage($validator->errors()->toArray());
            }
            $is_valid = false;
            $validated_data = [];
        } else {
            $validated_data = $validator->validated();
        }

        return [
            'is_valid' => $is_valid,
            'response' => $response,
            'validated_data' => $validated_data
        ];
    }

    /**
     * Check if a password is strong based on specified criteria.
     *
     * This function checks if a password meets the following criteria:
     * - Minimum length requirement.
     * - Contains at least one lowercase letter.
     * - Contains at least one uppercase letter.
     * - Contains at least one digit.
     * - Contains at least one special character (non-alphanumeric).
     *
     * @param string $password The password to check.
     * @param int $length The minimum length of the password (default is 6).
     * @return array An array indicating whether the password is strong and containing any errors.
     */
    public static function isPasswordStrong(string $password, int $length = 6): array
    {
        $errors = ["password" => [__("Your password should contain at least one lowercase letter, uppercase letter, digit, and special character.")]];

        if (strlen($password) < $length) {
            return [
                'strong' => false,
                'errors' => ["password" => ["Your password length must be at least $length characters."]]
            ];
        }

        if (!preg_match('/[a-z]/', $password)) {
            return [
                'strong' => false,
                'errors' => $errors
            ];
        }

        if (!preg_match('/[A-Z]/', $password)) {
            return [
                'strong' => false,
                'errors' => $errors
            ];
        }

        if (!preg_match('/[0-9]/', $password)) {
            return [
                'strong' => false,
                'errors' => $errors
            ];
        }

        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            return [
                'strong' => false,
                'errors' => $errors
            ];
        }

        return [
            'strong' => true,
            'errors' => []
        ];
    }
}
