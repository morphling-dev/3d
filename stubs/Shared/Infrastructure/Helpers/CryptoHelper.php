<?php

namespace Modules\Shared\Infrastructure\Helpers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class CryptoHelper
{
    /**
     * Encrypt sensitive data using Laravel's Crypt facade.
     *
     * @param  mixed  $data  The data to encrypt. Can be string, array, or serializable object.
     * @return string  The encrypted string representation of the data.
     */
    public static function encrypt(mixed $data): string
    {
        return Crypt::encrypt($data);
    }

    /**
     * Decrypt previously encrypted data with exception handling.
     *
     * @param  string  $payload  The encrypted data string.
     * @return mixed|null  The decrypted data, or null if decryption fails.
     */
    public static function decrypt(string $payload): mixed
    {
        try {
            return Crypt::decrypt($payload);
        } catch (\Exception $exception) {
            // Return null if decryption fails for any reason
            return null;
        }
    }

    /**
     * Generate a unique signature for integrations or webhook verification.
     *
     * @param  int  $length  The desired length of the signature in characters (must be even). Default is 64.
     * @return string  The generated unique hexadecimal signature.
     */
    public static function generateSignature(int $length = 64): string
    {
        // Ensure the length is even and greater than zero
        $length = $length > 0 ? $length : 64;
        if ($length % 2 !== 0) {
            $length++;
        }

        return bin2hex(random_bytes($length / 2));
    }
}
