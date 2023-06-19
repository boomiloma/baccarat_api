<?php


use App\Models\Customer;
use App\Models\File;
use App\Models\Language;
use App\Models\OntimePassword;
use App\Models\Translation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

const MODEL_PREFIX = "App\Models\\";
const TRASH_FOLDER = '/trash/';
const FILE_PERPOSE_1 = 1;
const FILE_PERPOSE_2 = 2;
const FILE_PERPOSE_3 = 3;
const FILE_PERPOSE_4 = 4;
const FILE_PERPOSE_5 = 5;
const FILE_PERPOSE_6 = 6;
const FILE_PERPOSE_7 = 7;
const FILE_PERPOSE_8 = 8;
const FILE_PERPOSE_9 = 9;
const FILE_PERPOSE_10 = 10;
const FILE_PERPOSE_TYPES = [
    FILE_PERPOSE_1 => 'Setting Logo',
    FILE_PERPOSE_2 => 'Twitter Icon',
    FILE_PERPOSE_3 => 'Pinterest Icon',
    FILE_PERPOSE_4 => 'Facebook Icon',
    FILE_PERPOSE_5 => 'Youtube Icon',
    FILE_PERPOSE_6 => 'Instagram Icon',
    FILE_PERPOSE_7 => 'QQ Icon',
    FILE_PERPOSE_8 => 'Skype Icon',
    FILE_PERPOSE_9 => 'Telegram Icon',
    FILE_PERPOSE_10 => 'Whatsapp Icon',
];
const TRANSFER_IN  = 'transfer_in';
const TRANSFER_OUT  = 'transfer_out';
const WITHDRAW  = 'withdraw';

if (!function_exists('getErrorMessages')) {
    function getErrorMessages($messages)
    {
        $errorMessages = [];
        foreach ($messages as $key => $values) {
            foreach ($values as $index => $value) {
                array_push($errorMessages, $value);
            }
        }

        return $errorMessages;
    }
}

if (!function_exists('generalErrorResponse')) {
    function generalErrorResponse(Exception $e)
    {
        Log::debug($e);
        return response()->json([
            'messages' => [$e->getMessage()],
            'trace' => [$e->getTrace()],
        ], 400);
    }
}
if (!function_exists('getArrayCollections')) {

    function getArrayCollections($arrayData)
    {
        $data = [];
        foreach ($arrayData as $key => $dt) {
            foreach ($dt as $d) {
                array_push($data, $d);
            }
        }

        return $data;
    }
}

if (!function_exists('paginate')) {
    function paginate($items, $perPage = 100, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}

if (!function_exists('formatIdd')) {
    function formatIdd($idd)
    {
        $idd = str_replace('+', '', $idd);
        $idd = '+' . $idd;

        return $idd;
    }
}
if (!function_exists('checkFileType')) {
    function checkFileType($file)
    {
        $type = 'file';
        if (substr($file->getMimeType(), 0, 5) == 'image') {
            $type = 'image';
        }

        return $type;
    }
}

if (!function_exists('getRandomIdGenerate')) {
    function getRandomIdGenerate($prefix = null)
    {
        return $prefix . '-' . Carbon::now()->timestamp . mt_rand(100, 99999);
    }
}

if (!function_exists('getRandomIdGenerator')) {
    function getRandomIdGenerator($prefix = null)
    {
        return $prefix . '-' . mt_rand(100, 99999999);
    }
}


if (!function_exists('uploadImage')) {
    function uploadImage($file, $folder)
    {

        if (!empty($file) && is_file($file)) {
            $md5Name = md5_file($file->getRealPath());
            $md5Name = Carbon::now()->timestamp . $md5Name;
            $guessExtension = $file->guessExtension();
            $uploaded_files = $file->storeAs('public/images/' . $folder, $md5Name . '.' . $guessExtension);
            $uploaded_files = substr(Storage::url($uploaded_files), 1);

            return $uploaded_files;
        }
        $file;
    }
}


/**
 * @desc soft delete relationship
 * @param $resource
 * @param $relations_to_cascade
 * @return mixed
 * @date 07 Jan 2023
 * @author Phen
 */
if (!function_exists('softDeleteRelations')) {
    function softDeleteRelations($resource, $relations_to_cascade)
    {
        if ($relations_to_cascade && is_array($relations_to_cascade)) {
            foreach ($relations_to_cascade as $relation) {
                if ($resource->{$relation}) {
                    if ($relation == 'file' or $relation == 'files' or $relation == 'image' or $relation == 'images') {
                        try {
                            foreach ($resource->{$relation}()->get() as $item) {
                                $data = $item->storage_path;
                                $trash_data = TRASH_FOLDER . $data;
                                //if is file, will move file to trash folder (safe delete can restore file later)
                                Storage::move($data, $trash_data);
                                $item->delete();
                            }
                        } catch (\Exception $e) {
                            Log::error("Delete relationship of table " . $resource->getTable() . " error: for relation name: " . $relation);
                            Log::error($e->getMessage());
                        }
                    } else {
                        try {
                            foreach ($resource->{$relation}()->get() as $item) {
                                $item->delete();
                                Log::debug("Deleted: " . $item->getTable());
                            }
                        } catch (\Exception $e) {
                            Log::error("Delete relationship of table " . $resource->getTable() . " error: for relation name: " . $relation);
                            Log::error($e->getMessage());
                        }
                    }
                }
            }
        }
    }
}

if (!function_exists('restoreRelations')) {
    function restoreRelations($resource, $relations_to_cascade)
    {
        foreach ($relations_to_cascade as $relation) {
            if (method_exists($resource, $relation)) {
                if ($relation == 'file' or $relation == 'files' or $relation == 'image' or $relation == 'images') {

                    try {
                        foreach ($resource->{$relation}()->withoutGlobalScope(SoftDeletingScope::class)->get() as $item) {
                            $data = $item->storage_path;
                            $trash_data = TRASH_FOLDER . $data;
                            Storage::move($trash_data, $data);
                            $item->restore();
                        }
                    } catch (\Exception $e) {
                        Log::error("Restore relationship of table " . $resource->getTable() . " error: for relation name: " . $relation);
                        Log::error($e->getCode());
                    }
                } else {
                    try {
                        foreach ($resource->{$relation}()->withoutGlobalScope(SoftDeletingScope::class)->get() as $item) {
                            $item->restore();
                        }
                    } catch (\Exception $e) {
                        Log::error("Restore relationship of table " . $resource->getTable() . " error: for relation name: " . $relation);
                        Log::error($e->getCode());
                    }
                }
            }
        }
    }
}

/**
 * @desc store files
 * @param $resource
 * @param $relations_to_cascade
 * @return mixed
 * @date 11 Jan 2023
 * @author Phen
 */
if (!function_exists('saveFiles')) {
    function saveFiles(object $masterModel, $fileRelation, $newFiles, array $fileFilter = [])
    {

        if ($newFiles) {
            if (!is_array($newFiles)) $newFiles = array($newFiles);
            if (sizeof($newFiles) > 0) {
                $files = $masterModel->{$fileRelation}();
                if ($fileFilter)
                    $files->where($fileFilter);

                $files = $files->get();

                if (sizeof($files) > 0) {
                    foreach ($files as $key => $file) {
                        $oldFile = $file->storage_path;
                        if (Storage::exists($oldFile)) Storage::delete($oldFile);
                        if (!empty($newFiles[$key])) {
                            $path = Storage::putFile('public/images/' . $masterModel->getTable(), $newFiles[$key]);

                            $file->update([
                                'path' =>  $path,
                                'type' => checkFileType($newFiles[$key])
                            ]);
                        }
                    }
                    //if old file less than new files
                    for ($key; $key < sizeof($newFiles) - 1; $key++) {
                        $path = Storage::putFile('public/images/' . $masterModel->getTable(), $newFiles[$key]);

                        $masterModel->{$fileRelation}()->create(
                            array_merge(
                                [
                                    'path' =>  $path,
                                    'type' => checkFileType($newFiles[$key])
                                ],
                                $fileFilter
                            )
                        );
                    }
                } else {
                    foreach ($newFiles as $key => $newFile) {
                        $path = Storage::putFile('public/images/' . $masterModel->getTable(), $newFile);

                        $masterModel->{$fileRelation}()->create(
                            $fileFilter ? array_merge(
                                [
                                    'path' =>$path ,
                                    'type' => checkFileType($newFile)
                                ],
                                $fileFilter
                            ) : [
                                'path' => $path,
                                'type' => checkFileType($newFile)
                            ]
                        );
                    }
                }
            }
        }
    }
}

if (!function_exists('deleteImage')) {
    function deleteImage($id)
    {
        $file = File::find($id);
        $url = env('APP_URL') . 'api/media/';
        $path = str_replace($url, '', $file->path);
        Storage::delete($path);
        $file->forceDelete();
    }
}


if (!function_exists('zeroappend')) {
    function zeroappend($LastNumber)
    {
        $count = (int) log10(abs($LastNumber)) + 1;
        if ($count == 1) {
            return $append = '000000';
        } elseif ($count == 2) {
            return $append = '00000';
        } elseif ($count == 3) {
            return $append = '0000';
        } elseif ($count == 4) {
            return $append = '000';
        } elseif ($count == 5) {
            return $append = '00';
        } elseif ($count == 6) {
            return $append = '0';
        } elseif ($count == 7) {
            return $append = '';
        } else {
            return $append = '';
        }
    }
}

if (!function_exists('getSuccessMessages')) {
    function getSuccessMessages($data, $status = true)
    {
        $successMessage = [];
        if (!empty($data['message'])) {
            $successMessage['message'] = $data['message'];
        }
        if (!empty($data['data'])) {
            $successMessage['data'] = $data['data'];
        }
        $successMessage['status'] = $status;

        return response()->json($successMessage, $data['statusCode']);
    }
}


if (!function_exists('getErrorMessagesMob')) {
    function getErrorMessagesMob($messages)
    {
        $errorMessages = [];
        foreach ($messages as $key => $values) {
            foreach ($values as $index => $value) {
                array_push($errorMessages, $value);
            }
        }

        return $errorMessages[0];
    }
}


const TRANSLATION_PURPOSE = [
    Language::class => [
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
        6 => 6,
        7 => 7,
        8 => 8,
        9 => 9
    ]
];


if (!function_exists('getTranslationPurpose')) {
    function getTranslationPurpose(string $model, $modelId)
    {
        return TRANSLATION_PURPOSE[$model][$modelId];
    }
}



///=====================================================Payment Utilities================================

/**
 * @desc getConfigs
 * @param $resource
 * @param $relations_to_cascade
 * @return integer
 * @date 27 Feb 2023
 * @author Phen
 */
if (!function_exists('getConfigs')) {
    function getConfigs()
    {
        $configs = [
            'url'               => 'https://devwebpayment.kesspay.io',
            'username'          => "lomatechnology2022@gmail.com",
            'password'          => '%6}.-MONeBKOuz-J]EKvuC^CA=%7K]h4F1=>F[$ARg._y@!|IintHQ2',
            'client_id'         => "00980f8f-fb0f-455f-bda8-b0615ba950b1",
            'client_secret'     => "rRb8s7JRbs+fyIy*jw(??.&-Lhm0iqje)J,?&ZJB(d9,tr=4.Uoo_|p",
            'seller_code'       => 'CU2302-28043196470682791',
            'api_secret_key'    => 's3:9EKp>!R9?9z%G_QmUjEQq,+}G~wkHw.FCMHR,pCwYzCWg-u<1nM.',
        ];
        return $configs;
    }
}


/**
 * @desc getConfigs
 * @param $resource
 * @param $relations_to_cascade
 * @return integer
 * @date 27 Feb 2023
 * @author Phen
 */
if (!function_exists('getToken')) {
    function getToken()
    {


        if (isset($_COOKIE['access_token'])) {
            return $_COOKIE['access_token'];
        }

        $params = [
            'grant_type' => "password",
            'client_id' => getConfigs()['client_id'],
            'client_secret' => getConfigs()['client_secret'],
            "username" => getConfigs()['username'],
            "password" => getConfigs()['password'],
        ];

        $url = getConfigs()['url'] . '/oauth/token';
        try {
            $resp = callHttp($url, $params);
            setcookie('access_token', $resp['access_token'], $resp['expires_in'] - 100);
            return $resp['access_token'];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

/**
 * @desc getConfigs
 * @param $resource
 * @param $relations_to_cascade
 * @return integer
 * @date 27 Feb 2023
 * @author Phen
 */
if (!function_exists('callHttp')) {
    function callHttp($url, $params)
    {

        try {

            $headers = ["Content-Type: application/json"];
            if (!str_contains($url, "oauth/token") && $token = getToken()) {
                $headers[] =  "Authorization: Bearer " . $token;
            }

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_HTTPHEADER =>  $headers,
                CURLOPT_SSL_VERIFYPEER => false
            ));

            $response = curl_exec($curl);

            if ($response === false) {
                throw new Exception(curl_error($curl));
            }

            curl_close($curl);

            return json_decode($response, true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}


/**
 * @desc encrypt
 * @param $resource
 * @param $relations_to_cascade
 * @return integer
 * @date 27 Feb 2023
 * @author Phen
 */
if (!function_exists('encrypt')) {
    function encrypt(array $params)
    {
        $rawText = json_encode($params);
        openssl_public_encrypt($rawText, $encrypted, getPublicKey());

        return bin2hex($encrypted);
    }
}


/**
 * @desc signature
 * @param $resource
 * @param $relations_to_cascade
 * @return integer
 * @date 27 Feb 2023
 * @author Phen
 */
if (!function_exists('signature')) {
    function signature(array $params, $api_secret_key)
    {
        $signType = $params['sign_type'] ?? "MD5";

        $string = toUrlParams($params);
        $string = $string . "&key=" . $api_secret_key;

        if ($signType == "MD5")
            $string = md5($string);
        else if ($signType == "HMAC-SHA256")
            $string = hash_hmac("sha256", $string, $api_secret_key);

        return $string;
    }
}

/**
 * @desc toUrlParams
 * @param $resource
 * @param $relations_to_cascade
 * @return integer
 * @date 27 Feb 2023
 * @author Phen
 */
if (!function_exists('toUrlParams')) {
    function toUrlParams(array $values)
    {
        ksort($values);

        $values = array_filter($values, function ($var) {
            return !is_null($var);
        });

        $buff = "";

        foreach ($values as $k => $v) {
            if ($k != "sign" && $v !== "" && !is_array($v) && !is_object($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");

        return $buff;
    }
}


/**
 * @desc getPublicKey
 * @param $resource
 * @param $relations_to_cascade
 * @return integer
 * @date 27 Feb 2023
 * @author Phen
 */
if (!function_exists('getPublicKey')) {
    function getPublicKey()
    {
        return "-----BEGIN PUBLIC KEY-----

    -----END PUBLIC KEY-----";
    }
}
///=====================================================Payment Utilities================================



if (!function_exists('generateUniqueSlug')) {
    function generateUniqueSlug( $model, $nameString, $modelSlugField = 'slug') {
        $uniqueSlug = $nameString;
        try {
            $uniqueSlug = Str::slug($nameString);
            $i = 1;
            $existingSlugs = $model->where($modelSlugField, $uniqueSlug)->pluck($modelSlugField)->toArray();
            while (in_array($uniqueSlug, $existingSlugs)) {
                $uniqueSlug = $uniqueSlug . '-' . $i;
                $i++;
            }
        }
        catch(Exception $th){

        }
        return $uniqueSlug;
    }
}



define('ADMIN', 'admin');
