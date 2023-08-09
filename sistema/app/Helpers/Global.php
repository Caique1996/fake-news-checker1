<?php

use App\Models\Api;
use App\Models\ApiRequest;
use App\Models\Country;
use App\Models\MetaData;
use App\Models\User;
use App\Services\RateLimitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Pdp\Rules;


function separateByCommas(array $arr): ?string
{
    return implode(",", $arr);
}

function clearSensitive($rowVal)
{
    $len = strlen($rowVal);
    if ($len <= 20) {
        $newValue = str_repeat("*", strlen($rowVal));
    } else {
        $newValue = $rowVal;
        $qtyChar = 4;
        $newValue = substr($newValue, 0, $qtyChar) . str_repeat("*", strlen($rowVal) - $qtyChar);
    }
    return $newValue;;

}

function clearSensitiveData($data)
{
    $keys = ['password', 'auth_key', 'pass', 'key'];
    foreach ($keys as $k) {
        if (isset($data[$k])) {
            $data[$k] = clearSensitive($data[$k]);;
        }
    }
    return $data;


}

function defaultApiResponse($data): JsonResponse
{
    if (isset($data['success']) && $data['success'] === false) {
        return response()->json($data, 422);
    } elseif (isset($data['success']) && $data['success'] === true) {
        return response()->json($data);
    } else {
        return response()->json($data);
    }
}

function searchInArrayWithArray($search, $array): bool
{
    $matchs = 0;
    $total = count($search);
    foreach ($search as $word) {
        if (in_array($word, $array)) {
            $matchs++;
        }
    }
    if ($matchs >= $total) {
        return true;
    }
    return false;
}

function successFlashNotification($msg): void
{
    Alert::success($msg)->flash();
}

function errorFlashNotification($msg): void
{

    Alert::error($msg)->flash();
}

function getUserActor(): ?User
{
    try {
        $route = Route::current();
        if (is_null($route)) {
            return null;
        }
        $middleWare = Route::current()->gatherMiddleware();
        if (searchInArrayWithArray(['web', 'admin'], $middleWare)) {
            return getAdminUser();
        } elseif (searchInArrayWithArray(['web', 'auth.api'], $middleWare)) {
            return getApiUser();
        }
    } catch (Exception $e) {
        return null;
    }

    return null;
}

function translateValues(array $arr): ?array
{
    foreach ($arr as $key => $value) {
        $arr[$key] = __($value);
    }
    return $arr;
}

function transManual($key)
{
    return trans($key, [], null, false);
}

function getUserIp(): ?string
{
    $ip = null;
    if (getenv('HTTP_CF_CONNECTING_IP')) {
        $ip = getenv('HTTP_CF_CONNECTING_IP');
    } else if (getenv('HTTP_CLIENT_IP')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } else if (getenv('HTTP_X_FORWARDED_FOR')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } else if (getenv('HTTP_X_FORWARDED')) {
        $ip = getenv('HTTP_X_FORWARDED');
    } else if (getenv('HTTP_FORWARDED_FOR')) {
        $ip = getenv('HTTP_FORWARDED_FOR');
    } else if (getenv('HTTP_FORWARDED')) {
        $ip = getenv('HTTP_FORWARDED');
    } else if (getenv('REMOTE_ADDR')) {
        $ip = getenv('REMOTE_ADDR');
    }
    if (!$ip || $ip === '::1') {
        $ip = request()->ip();
    }
    return $ip;
}

function getAdminUser(): ?User
{
    return backpack_user();
}

function getApi(): ?Api
{
    $apiToken = request()->header('api-token');

    return Api::where('token', $apiToken)->first();
}

function isFullEmpty($var): bool
{
    if (is_array($var) && count($var) == 0) {
        return true;
    }
    if (is_null($var)) {
        return true;
    }
    if ($var === false) {
        return false;
    }
    if ($var === 0) {
        return false;
    }
    return empty($var);
}

function getApiUser(): ?User
{
    $api = getApi();
    if (isset($api['id'])) {
        return $api->user()->first();
    }
    return null;

}

function isLocalEnv(): bool
{
    if (env("APP_ENV") == 'local') {
        return true;
    }
    return false;
}

function registerException($ex): string
{
    return \App\Models\Exception::register($ex);
}

function registerExceptionAndAbort($ex): void
{
    registerException($ex);
    abort(500);
}

function errorMsg($msg): string
{
    return '<div class="alert alert-danger mb-0" role="alert"><span class="alert-inner--icon me-2"><i
                                        class="fe fe-slash"></i></span> <span
                                    class="alert-inner--text">' . e($msg) . '
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                            </div><br>';

}

function infoMsg($msg): string
{
    return '<div class="alert alert-info mb-0" role="alert"><span class="alert-inner--icon me-2"><i
                                        class="fe fe-info"></i></span> <span
                                    class="alert-inner--text">' . e($msg) . '
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                            </div><br>';

}

function warningMsg($msg): string
{
    return '<div class="alert alert-warning mb-0" role="alert"><span class="alert-inner--icon me-2"><i
                                        class="fe fe-alert-triangle"></i></span> <span
                                    class="alert-inner--text">' . e($msg) . '
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                            </div><br>';

}

function popoverInfo($title, $msg): string
{
    return '<span style="cursor:pointer"  data-bs-popover-color="head-primary"  data-bs-container="body"
                                            data-bs-content="' . $msg . '"
                                            data-bs-placement="right"  data-toggle="popover"
                                            data-bs-toggle="popover" title="" data-bs-original-title="' . $title . '"><i class="fa fa-info-circle text-info"></i>
    </span>';
}

function showCaptcha(): string
{
    return ' <div class="captcha-box" style="width: 100%">
                            <div class="g-recaptcha" data-callback="setToken" data-sitekey="' . env('NOCAPTCHA_CLIENT') . '" data-theme="light"
                                 style=" transform:scale(1);-webkit-transform:scale(1);transform-origin:0 0;-webkit-transform-origin:0 0;"></div>
                        </div>';
}

function showInputError($message): string
{
    return '<div class="invalid-feedback2">' . e($message) . '</div>';
}

function deniedEspecialCharsForDomain()
{
    return [
        ',', '|', '!', '"', "'", "@", "#", "$", "%", "°", "{", "}",
        "{", "+", "(", ")", " ", "/", "\\", "`", "~", ";", "<", ">", ":", "?", "=", "&", '*.*'
    ];
}

function isValidDomain($domain): bool
{

    try {
        if (isset($domain[0]) && $domain[0] == "-") {
            return false;
        }
        if (Str::contains($domain, deniedEspecialCharsForDomain())) {
            return false;
        }
        if (!filter_var($domain, FILTER_VALIDATE_DOMAIN)) {
            return false;
        }
        $publicSuffixList = Rules::fromPath(base_path('files/public_suffix/public_suffix_list.dat'));
        $result = $publicSuffixList->getCookieDomain($domain);
        return $result->suffix()->isKnown();
    } catch (Exception) {
        return false;
    }
}

function apiErrorMessageValidation($msg, $data = []): array
{

    $response = ['success' => false, 'message' => $msg];
    foreach ($data as $rows) {
        foreach ($rows as $row) {
            $response['data'][] = $row;
        }

    }

    ApiRequest::insertApiRequest($response);

    return $response;
}

function apiErrorMessage($msg, $data = []): array
{

    $response = ['success' => false, 'message' => $msg, 'data' => $data];
    ApiRequest::insertApiRequest($response);

    return $response;
}

function apiSuccessMessage(array $data, $msg = 'Operation performed successfully.'): array
{
    $response = ['success' => true, 'message' => __($msg), 'data' => clearSensitiveData($data)];
    ApiRequest::insertApiRequest($response);
    return $response;
}

function ipInfoLink($ip): ?string
{
    $link = "https://tools.keycdn.com/geo?host=$ip";
    return html_ahref($ip, $link);
}


function getAllIds($rows): ?array
{
    $ids = [];
    foreach ($rows as $r) {
        $ids[$r->id] = $r->id;
    }
    return $ids;
}

function getModelName($model): ?string
{
    if (is_string($model)) {
        $className = addslashes($model);
    } else {
        $className = addslashes(get_class($model));
    }
    $className = str_replace("\\", "", $className);
    return str_replace("AppModels", "", $className);

}

function removeIndexByValue($array, $search): ?array
{
    $values = [];
    foreach ($array as $index => $value) {
        if ($value != $search) {
            $values[] = $value;
        }
    }
    return $values;
}

function formatToBrl($amount): bool|string
{
    $formatter = new NumberFormatter('pt_BR', NumberFormatter::CURRENCY);
    return $formatter->formatCurrency($amount, 'BRL');
}

function generateHash(): string
{
    $hash = sortHashCod(64);
    return strtoupper(substr(hash('sha256', $hash), 0, 16));
}

function getErrorMessages($errors)
{
    $messages = [];
    foreach ($errors as $key => $error) {
        $messages[] = $error;
    }
    return $messages;
}

function sortHashCod($limit = 8): string
{
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < $limit; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string

}

function getRandomHash($qntd = 64): string
{
    return Str::random($qntd);
}

function generateDownloadCode(): string
{
    return hash("sha256", getRandomHash(60));;
}

function explodeComma($data): array
{
    return explode(",", $data);
}

function implodeComma($data): string
{
    return implode(",", $data);
}

function generateDcvUniqueCode(): string
{
    return strtoupper(substr(md5(Str::random(50)), 0, 20));
}

function is_base64($data): bool
{
    $decoded_data = base64_decode($data, true);
    $encoded_data = base64_encode($decoded_data);
    if ($encoded_data != $data) return false;
    else if (!ctype_print($decoded_data)) return false;

    return true;
}

function getRowsNames($rows): array
{
    $ids = [];
    foreach ($rows as $row) {
        if (isset($row['id'])) {
            $ids[] = $row['id'];
        }
    }
    return $ids;
}

function isPublicIp($ip): bool
{
    return filter_var(
        $ip,
        FILTER_VALIDATE_IP,
        FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
    );
}

function getRowsIds($rows): array
{
    $ids = [];
    foreach ($rows as $row) {
        if (isset($row['id'])) {
            $ids[] = $row['id'];
        }
    }
    return $ids;
}

function removeExtraPrecision($value): string
{
    return bcmul($value, 1, 2);
}

function getPeriods(): array
{

    $periods = [1, 3, 6, 12, 24, 36, 48, 60, 72];
    $formatedPeriods = [];
    foreach ($periods as $p) {
        $formatedPeriods[$p] = $p . " " . __("month(s)");
    }
    return $formatedPeriods;
}

function generateOptionIdAndName($rows): array
{
    $products = [];
    foreach ($rows as $row) {
        $products[$row->id] = $row->name;
    }
    return $products;
}


function checkLive($url): bool
{
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $http_respond = trim(strip_tags(curl_exec($ch)));
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (str_contains($http_code, '20') || str_contains($http_code, '30') || str_contains($http_code, '40') || str_contains($http_code, '500')) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $ex) {
        return false;
    }
}

function getPageContent($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return data inplace of echoing on screen
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // Skip SSL Verification
    $apiResponse = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $apiResponse;

}

function formatPagination($model)
{
    $orders = json_decode($model->paginate(25)->toJson(), true);
    $response = [];
    $response['data'] = $orders['data'];
    $paginationKeys = ['total', 'per_page', 'current_page', 'last_page', 'from', 'to'];
    foreach ($paginationKeys as $k) {
        if (isset($orders[$k])) {
            $response[$k] = $orders[$k];
        }
    }
    $response['rows'] = [];
    return $response;
}

function clearUrl($url)
{
    $url = str_replace(['https://', 'http://'], '', $url);
    $url = rtrim($url, '/');
    return $url;
}

function getHtmlFromUrl(string $url)
{
    $cleanUrl = clearUrl($url);
    $data = HtmlFile::where("url", $cleanUrl)->order("id", "desc")->first();
    if (isset($data['id'])) {

    } else {
        $html = Http::get($url)->getBody();

    }

}

function getHostFromUrl(string $url): ?string
{
    $domain = parse_url('http://' . clearUrl($url));
    if (isset($domain['host'])) {
        return $domain['host'];
    } elseif (isset($domain['path'])) {
        return $domain['path'];
    } else {
        return null;
    }


}

function getUrlProtocol($url)
{
    $parts = explode("://", $url);
    if (isset($parts[0])) {
        return strtolower($parts[0]);
    }
    return null;
}

function isValidUrl($url)
{
    return filter_var($url, FILTER_VALIDATE_URL);
}

function getCleanNewsUrl($url)
{
    $urlParts = explode("#", $url);
    $newUrl = $urlParts[0];
    $lastChar = substr($newUrl, -1);
    if ($lastChar == "/") {
        $newUrl = rtrim($newUrl, "/");;
    }
    return $newUrl;
}

function isValidHttpsUrl($url)
{
    $urlParts = parse_url($url);
    if (isValidUrl($url) && isset($urlParts['host']) && isset($urlParts['scheme']) && $urlParts['scheme'] == 'https') {
        return true;
    }
    return false;

}

function imgSrcHtml($src)
{
    if (is_null($src)) {
        return null;
    }
    if (str_contains($src, '://')) {
        $link = $src;
    } else {
        $link = url('storage/uploads/' . $src);
    }
    return "<img src='$link' class='img-responsive' width='150' />";
}

function getImageLink($src)
{
    return url('storage/uploads/' . $src);
}

function hrefImgSrcHtml($src)
{
    if (is_null($src)) {
        return null;
    }
    if (str_contains($src, '://')) {
        $link = $src;
    } else {
        $link = getImageLink($src);
    }
    return html_ahref("<img src='$link' class='img-responsive' width='150' />", $link, true);
}

function imageWithHash($image, $checksum)
{
    return hrefImgSrcHtml($image) . "<p>ID:<b>" . $checksum . "</b></p>";
}

function manageBtn($route, $id)
{
    $link = route($route, ['id' => $id]);
    return customHtmlLink($link, 'la la-cog text-warning', __("manage"), ['class' => 'btn btn-sm btn-link text-success text-capitalize']);
}

function checkNews($searchId)
{
    $user = backpack_user();
    $link = route("review.create") . '?user_id=' . $user->id . '&search_id=' . $searchId;
    return customHtmlLink($link, 'la la-check-double text-success', __("Check"));
}

function getExternalResultsLink($searchId)
{
    $link = 'google-search-result?search_id=' . $searchId;
    $countResults=\App\Models\GoogleSearchResult::where("search_id",$searchId)->count();
    if($countResults>0){
        return customHtmlLink($link, 'la la-newspaper text-warning', __("External Results"));

    }
    return "";
}


function getMonthByPtName($ptName)
{
    $ptName = ucfirst(strtolower($ptName));
    $meses_abreviado = array(
        'jan' => 1,
        'Fev' => 2,
        'Mar' => 3,
        'Abr' => 4,
        'Mai' => 5,
        'Jun' => 6,
        'Jul' => 7,
        'Ago' => 8,
        'Set' => 9,
        'Out' => 10,
        'Nov' => 11,
        'Dez' => 11
    );
    return date("m", strtotime(date("Y") . "-" . $meses_abreviado[$ptName] . "-01"));

}

function convertGoogleDate($url)
{
    $parts = explode("...", $url);
    if (isset($parts[0])) {
        $date = str_replace([" de "], "-", $parts[0]);
        $date = str_replace([".", " "], "", $date);
        $dateParts = explode("-", $date);
        if (count($dateParts) >= 2) {
            try {
                $month = getMonthByPtName($dateParts[1]);
                $date = $dateParts[0] . '-' . $month . '-' . $dateParts[2];
                $dateTime = new DateTime($date);
                return $dateTime->format("Y-m-d");
            } catch (\Exception $e) {
                return "-";
            }

        }
    }
    return "-";

}

function getAnyIndexArray(?array $rows, ?array $indexList)
{
    foreach ($indexList as $index) {
        if (is_string($index) && isset($rows[$index])) {
            return $rows[$index];
        }
    }
    return null;
}

function uploadImage($request)
{
    $fileInput = $request->file('image');
    $extension = $fileInput->getClientOriginalExtension();
    $imageId = md5(file_get_contents($fileInput->getPathName()));
    $path = 'public/uploads/' . $imageId . "." . $extension;
    $newDir = storage_path('app/' . $path);
    if (file_exists($newDir)) {
        return $path;
    }
    return $request->file('image')->storeAs('public/uploads', $imageId . "." . $extension);
}

function formatDateToDb($date)
{
    $date = new DateTime($date);
    return $date->format("Y-m-d");
}
