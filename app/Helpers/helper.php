<?php

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

if (!function_exists('generateReference')) { /* Check_for "generateReference" */
    function generateReference()
    {
        $reference = (string) Str::uuid();
        $reference = str_replace('-', '', $reference);

        return $reference;
    }
} /* End_check for "generateReference" */

if (!function_exists('isApiRequest')) { /* Check_for "isApiRequest" */
    function isApiRequest()
    {
        if (request()->wantsJson() || str_starts_with(request()->path(), 'api')) {
            return true;
        }

        return false;
    }
} /* End_check for "isApiRequest" */

if (!function_exists('getRealIp')) { /* Check_for "getRealIp" */
    function getRealIp()
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return request()->ip(); // it will return server ip when no client ip found
    }
}

if (!function_exists('uploadViaUrl')) { /* Check_for "uploadViaUrl" */
    function uploadViaUrl($disk, $url, $kyc)
    {
        if (!empty($url) && !empty($disk)) {
            try {
                // $name = strtotime(date('h:i:s')) . '.png';
                // $image = Storage::disk($disk)->put($name, file_get_contents($url));

                // Assign a default name for the file to be extacted from the URL
                $file = strtotime(date('h:i:s')) . '.png';

                // Directory for images to be uploaded on S3
                $filePath = 'images/kyc-documents/' . $file;

                $updloadFile = Storage::disk('s3')->put($filePath, file_get_contents($url));

                // URL to the saved file AWS S3 bucket
                $fileAWSURL = Storage::disk('s3')->url($filePath);

                if ($updloadFile) {
                    // Update the KYC record status
                    $kyc->update([
                        'pushed'    => 'Yes',
                        'image'     => $fileAWSURL
                    ]);
                }

                return $fileAWSURL;
            } catch (\Exception $e) {
                sendToSlack($e);
                return false;
            }
        }
    }
}

if (!function_exists('decodeResponse')) { /* Check_for "decodeResponse" */
    function decodeResponse($response, $object = true)
    {
        if ($object == false) {
            return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        }

        return (object) json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }
}

if (!function_exists('decodeWebhook')) { /* Check_for "decodeWebhook" */
    function decodeWebhook()
    {
        $data = trim(file_get_contents('php://input'), "\xEF\xBB\xBF");
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }
}

if (!function_exists('sendMailByDriver')) { /* Check_for "sendMailByDriver" */
    function sendMailByDriver($driver, $email, $data)
    {
        // Try and send the mail via the selected dirver
        {
            // Mailgun drivers
            $mailgunDrivers = ['mailgun', 'onboarding_mailgun', 'marketing_mailgun', 'transaction_mailgun'];

            // Criteria to lookout for in the selected mail driver
            $criteria = in_array($driver, $mailgunDrivers) ? config('mail.mailers.' . $driver . '.domain') : config('mail.mailers.' . $driver . '.username');

            // Verify if the driver exist in the env and mail configuration file
            if (!is_null($criteria)) {
                // Try and send the mail via the selected dirver
                try {
                    Mail::mailer($driver)->to($email)->send($data);

                    return true;
                } catch (\Exception $e) {
                    // Log the driver mail error
                    logger($driver == 'smtp' ? 'Mailtrap' : 'Mailgun' . ' Failure => ', [
                        'message' => $e->getMessage(),
                    ]);

                    return false;
                }
            }

            logger(ucfirst($driver) . ' driver configuration is empty.');
            return false;
        }
    }
}

if (!function_exists('removeComma')) { /* Check_for "removeComma" */
    /**
     * Remove comma from number format without removing decimal point.
     *
     * @param string  $value
     * @return int  $value
     */
    function removeComma($value)
    {
        return floatval(preg_replace('/[^\d.]/', '', $value));
    }
}

if (!function_exists('removeDoubleSpacing')) { /* Check_for "removeDoubleSpacing" */
    /**
     * Remove double spacing from a string.
     *
     * @param string  $string
     * @return string  $string
     */
    function removeDoubleSpacing($string)
    {
        return Str::of($string)->replaceMatches('/ {2,}/', ' ');
    }
}

if (!function_exists('explodeString')) { /* Check_for "explodeString" */
    /**
     * Remove unwanted characters from a string.
     *
     * @param string  $string
     * @return string  $string
     */
    function explodeString($string)
    {
        // Exploade the string
        $result = explode(', ', $string);

        // Convert array resukt back to string
        $result = $result[0];

        if (str_contains($result, ',')) {
            $result = Str::replace(',', ' ', $result);
        }

        if (str_contains($result, '-')) {
            $result = Str::replace('-', ' ', $result);
        }

        if (str_contains($result, '_')) {
            $result = Str::replace('_', ' ', $result);
        }

        // Removes trailing whitespaces spaces from the beginning and the end of the string
        $result = rtrim($result);
        $result = ltrim($result);

        // Remove double spaces
        return removeDoubleSpacing($result);
    }
}

if (!function_exists('toSentenceCase')) { /* Check_for "toSentenceCase" */
    function toSentenceCase($sentence)
    {
        return ucfirst(strtolower($sentence));
    }
}

if (!function_exists('customStripTags')) { /* Check_for "customStripTags" */
    /**
     * Remove HTML tags and replace new lines from strings.
     *
     * @param string  $string
     * @return string  $string
     */
    function customStripTags($string)
    {
        $description = strip_tags(strval($string));
        $description = str_replace("'", "", $description);
        $description = str_replace('"', "", $description);
        $description = str_replace(':', "", $description);
        $description = str_replace('’', "", $description);
        $description = str_replace('\'', ' ', $description);
        $description = str_replace('/', ' ', $description);
        $description = str_replace('-', ' ', $description);
        $description = trim(preg_replace('/\s+/', ' ', $description));

        return preg_replace('/\s/', ' ', $description);
    }
}

if (!function_exists('encryptValue')) {
    /**
     * Encrypt a value.
     *
     * @param mixed $value
     * @return string
     */
    function encryptValue($value)
    {
        return encrypt($value);
    }
}

if (!function_exists('decryptValue')) {
    /**
     * Decrypt a value.
     *
     * @param string $encryptedValue
     * @return mixed
     */
    function decryptValue($encryptedValue)
    {
        return decrypt($encryptedValue);
    }
}

if (!function_exists('getShortName')) {
    /**
     * Create shortname.
     *
     * @param string $shortName
     * @return mixed
     */
    function getShortName($fullName)
    {
        $nameParts = explode(' ', $fullName);
        $shortName = '';

        foreach ($nameParts as $part) {
            $shortName .= strtoupper(substr($part, 0, 1));
        }

        return $shortName;
    }
}

if (!function_exists('authenticateWebhook')) {
    /**
     * Authenticates the forwarded webhook from Purse version 1.0.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    function authenticateWebhook($request)
    {
        // Extract the neccessary keyes from the incoming request headers
        $signature = $request->header('purse');
        $key = $request->header('x-api-key');
        $cipher = $request->header('x-api-cipher');

        if (empty($signature) || empty($cipher) || empty($key)) {
            return response()->json(['success' => false, 'message' => 'Please provide the complete authorization tokens.'], Response::HTTP_NOT_FOUND);
        }

        if (Str::startsWith($key, 'base64:')) {
            $key = Str::replace('base64:', '', $key);
        }

        try {
            $customEncryption = new \Illuminate\Encryption\Encrypter(base64_decode($key), $cipher);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Invalid webhook key or unsupported cipher.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $decrytpedSignature = $customEncryption->decrypt($signature);

        return strcmp($decrytpedSignature, config('constants.purse_webhook_signature')) == 0 ? true : false;
    }
}

if (!function_exists('lockTable')) {
    /**
     * Lock a table
     * @param string  $tableName
     * @param string  $columName
     */
    function lockTable(int $userId, string $tableName, string $columName)
    {
        return DB::table($tableName)
            ->where($columName, $userId)
            ->lockForUpdate()
            ->first();
    }
}

if (!function_exists('unLockTables')) {
    function unLockTables()
    {
        DB::raw('unlock tables');
    }
}

if (!function_exists('generateTemporaryLink')) {
    function generateTemporatyLink($user, $route, $duration)
    {
        return \Illuminate\Support\Facades\URL::temporarySignedRoute(
            $route,
            Carbon::now()->addMinutes(\Illuminate\Support\Facades\Config::get('auth.verification.expire', $duration)),
            [
                'id' => $user->uuid,
                'hash' => sha1($user->email),
            ]
        );
    }
}

if (!function_exists('customPagination')) {
    /**
     * @param int  $pnumberOfItems
     * @param Illuminate\Support\Collection $collection
     */
    function customPagination(int $numberOfItems, $collection)
    {
        // Define the number of items per page
        $perPage = $numberOfItems;

        // Get the current page number from the request
        $currentPage = Paginator::resolveCurrentPage();

        // Manually create a paginator instance for the transformed data
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $collection->forPage($currentPage, $perPage),
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
            ]
        );
    }
}

if (!function_exists('stripSpecialCharactersAndWhitespace')) { /* Check_for "stripSpecialCharactersAndWhitespace" */
    /**
     * Remove HTML tags, special characters, replace new lines, and remove whitespace from strings.
     *
     * @param string  $string
     * @return string  $string
     */
    function stripSpecialCharactersAndWhitespace($string)
    {
        return preg_replace("/\s+/", "", customStripTags($string));
    }
}

if (!function_exists('getS3RelativePath')) { /* Check_for "getS3RelativePath" */
    /**
     * .Get path to the file relative to the root of the S3 bucket:
     *
     * @param string  $string
     * @return string  $string
     */
    function getS3RelativePath($url)
    {
        // Get the S3 bucket name from the URL
        $bucket = str_replace('.s3.amazonaws.com', '', parse_url($url, PHP_URL_HOST));

        // Get the path to the file on S3
        $path = parse_url($url, PHP_URL_PATH);

        // Remove the leading slash from the path
        $path = ltrim($path, '/');

        // Remove the bucket name from the path
        $path = str_replace($bucket . '/', '', $path);

        return $path;
    }
}

if (!function_exists('uploadToS3')) {
    /**
     * Upload a file to Amazon S3 and return the file URL.
     *
     * @param Illuminate\Http\UploadedFile $file The file to be uploaded.
     * @param string $path The path where the file will be stored in S3.
     * @param string $disk The name of the S3 disk defined in config/filesystems.php.
     * @return string|null The URL of the uploaded file, or null if the upload fails.
     */
    function uploadToS3($file, $path, $oldFileUrl = null)
    {
        try {
            $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $filePath = $path . '/' . $fileName;

            // Push to S3 bucket
            if ((env('APP_ENV') !== 'local')) {
                // If an old file URL is provided, delete the old file from S3
                if ($oldFileUrl) {
                    // Extract the path from the URL
                    $oldFilePath = parse_url($oldFileUrl, PHP_URL_PATH);
                    if (Storage::disk('s3')->exists($oldFilePath)) {
                        Storage::disk('s3')->delete($oldFilePath);
                    }
                }

                // Upload the new file to S3
                Storage::disk('s3')->put($filePath, file_get_contents($file));

                // Get the URL of the uploaded file
                $fileUrl = Storage::disk('s3')->url($filePath);

                // strip out all the details that gives off the bucket id and stuff
                $fileUrl = getS3RelativePath($fileUrl);

                // return the url back to where it is needed
                return $fileUrl;
            }

            // Push to local storage
            Storage::put($filePath, file_get_contents($file));
            return $filePath;
        } catch (\Exception $e) {
            sendToSlack($e);
            return null;
        }
    }
}

if (!function_exists('findObjectFromArray')) {
    /**
     * Find the object in an array with the given key and value
     *
     * @param string  $key
     * @param string  $value
     * @param array  $array
     *
     * @return mixed  $result
     */
    function findObjectFromArray($key, $value, $array)
    {
        $result = array_filter($array, function ($sub) use ($value, $key) {
            return array_key_exists($key, $sub) && $sub[$key] == $value;
        });

        $result = array_search($value, array_column($array, $key));

        // If key is found, return the array
        if ($result !== false) {
            return $array[$result];
        }

        // If key is not found, return false
        return false;
    }
}

if (!function_exists('revokeAccessToken')) {
    function revokeAccessToken($tokenId)
    {
        $tokenRepository = app('Laravel\Passport\TokenRepository');
        $refreshTokenRepository = app('Laravel\Passport\RefreshTokenRepository');

        $tokenRepository->revokeAccessToken($tokenId);
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);

        DB::table('oauth_access_tokens')->where('id', $tokenId)->delete();
    }
}

if (!function_exists('throwException')) {
    /**
     * Throw an error exception response
     *
     * @param string  $message
     * @param int  $code
     */
    function throwException($message, int $code = Response::HTTP_UNPROCESSABLE_ENTITY)
    {
        // Trim and transform the message to sentence case
        $message = toSentenceCase(trim($message));

        if (isApiRequest()) {
            throw new HttpResponseException(response()->json([
                'success'   => false,
                'message'   => $message,
            ], $code));
        }

        throw new GeneralException($message);
    }
}
