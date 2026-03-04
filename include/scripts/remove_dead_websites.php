<?php

declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "This script must be run from CLI.\n");
    exit(1);
}

require_once(__DIR__ . '/../config.php');

spl_autoload_register(function ($class) {
    $prefixes = [
        'Databases\\' => __DIR__ . '/../classes/Databases/',
        'DatabasesSchemes\\' => __DIR__ . '/../classes/DatabasesSchemes/',
        'DatabasesLocation\\' => __DIR__ . '/../classes/DatabasesLocation/',
        'Connectors\\' => __DIR__ . '/../classes/Connectors/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (strpos($class, $prefix) === 0) {
            $relativeClass = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (is_file($file)) {
                require_once($file);
            }
            return;
        }
    }
});

$dryRun = in_array('--dry-run', $argv, true);

$membersExtras = new \Databases\MembersExtras();
$rows = $membersExtras->selectWhere(['website !=' => ''], 'user_id ASC');

$checkedCount = 0;
$removedCount = 0;
$updatedCount = 0;
$skippedCount = 0;

foreach ($rows as $row) {
    $checkedCount++;
    $rawWebsite = trim((string)$row['website']);
    $candidateWebsites = websiteCandidates($rawWebsite);

    if ($candidateWebsites === []) {
        continue;
    }

    $reachableWebsite = '';
    $status = 0;
    $hasDefinitiveStatus = false;
    $lastErrorMessage = '';
    foreach ($candidateWebsites as $candidateWebsite) {
        $probe = urlStatusCode($candidateWebsite);
        $status = $probe['status'];
        if ($probe['error_message'] !== '') {
            $lastErrorMessage = $probe['error_message'];
        }

        if ($status > 0 || isDefinitiveCurlError($probe['error_code'])) {
            $hasDefinitiveStatus = true;
        }
        if ($status > 0 && $status < 400) {
            $reachableWebsite = $candidateWebsite;
            break;
        }
    }

    if (!$hasDefinitiveStatus) {
        echo sprintf(
            "[SKIP] user_id=%d website=%s status=unknown%s\n",
            (int)$row['user_id'],
            $rawWebsite,
            $lastErrorMessage !== '' ? ' error=' . $lastErrorMessage : ''
        );
        $skippedCount++;
        continue;
    }

    if ($reachableWebsite === '') {
        echo sprintf("[REMOVE] user_id=%d website=%s status=%d\n", (int)$row['user_id'], $rawWebsite, $status);
        if (!$dryRun) {
            $membersExtras->updateWhere(['website' => ''], ['user_id' => (int)$row['user_id']]);
        }
        $removedCount++;
        continue;
    }

    if ($reachableWebsite !== $rawWebsite) {
        echo sprintf("[UPDATE] user_id=%d %s -> %s\n", (int)$row['user_id'], $rawWebsite, $reachableWebsite);
        if (!$dryRun) {
            $membersExtras->updateWhere(['website' => $reachableWebsite], ['user_id' => (int)$row['user_id']]);
        }
        $updatedCount++;
    }
}

echo "\nSummary:\n";
echo sprintf("Checked: %d\n", $checkedCount);
echo sprintf("Updated to HTTPS/normalized: %d\n", $updatedCount);
echo sprintf("Removed dead URLs: %d\n", $removedCount);
echo sprintf("Skipped unknown/unreachable URLs: %d\n", $skippedCount);
echo sprintf("Mode: %s\n", $dryRun ? 'dry-run' : 'live');

function websiteCandidates(string $website): array
{
    $website = trim($website);
    if ($website === '') {
        return [];
    }

    $candidates = [];

    if (preg_match('/^https?:\/\//i', $website)) {
        $candidates[] = $website;

        if (stripos($website, 'https://') === 0) {
            $candidates[] = 'http://' . substr($website, 8);
        } else {
            $candidates[] = 'https://' . substr($website, 7);
        }
    } else {
        $candidates[] = 'https://' . $website;
        $candidates[] = 'http://' . $website;
    }

    $validatedCandidates = [];
    foreach ($candidates as $candidate) {
        $validated = filter_var($candidate, FILTER_VALIDATE_URL);
        if ($validated !== false && !in_array($validated, $validatedCandidates, true)) {
            $validatedCandidates[] = $validated;
        }
    }

    return $validatedCandidates;
}

/**
 * @return array{status:int,error_code:int,error_message:string}
 */
function urlStatusCode(string $url): array
{
    $ch = curl_init($url);
    if ($ch === false) {
        return ['status' => 0, 'error_code' => 0, 'error_message' => 'curl_init_failed'];
    }

    curl_setopt_array($ch, [
        CURLOPT_NOBODY => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_USERAGENT => 'quiltsco-url-cleaner/1.0',
    ]);

    curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $errorCode = curl_errno($ch);
    $errorMessage = curl_error($ch);
    curl_close($ch);

    if ($errorCode !== 0) {
        if (isSslRelatedCurlError($errorCode)) {
            return urlStatusCodeInsecure($url, $errorCode, $errorMessage);
        }

        return [
            'status' => 0,
            'error_code' => $errorCode,
            'error_message' => sprintf('curl_%d:%s', $errorCode, $errorMessage),
        ];
    }

    if ($status === 405) {
        return urlStatusCodeGet($url);
    }

    return ['status' => $status, 'error_code' => 0, 'error_message' => ''];
}

/**
 * @return array{status:int,error_code:int,error_message:string}
 */
function urlStatusCodeGet(string $url): array
{
    $ch = curl_init($url);
    if ($ch === false) {
        return ['status' => 0, 'error_code' => 0, 'error_message' => 'curl_init_failed'];
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_USERAGENT => 'quiltsco-url-cleaner/1.0',
        CURLOPT_RANGE => '0-0',
    ]);

    curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $errorCode = curl_errno($ch);
    $errorMessage = curl_error($ch);
    curl_close($ch);

    if ($errorCode !== 0) {
        if (isSslRelatedCurlError($errorCode)) {
            return urlStatusCodeGetInsecure($url, $errorCode, $errorMessage);
        }

        return [
            'status' => 0,
            'error_code' => $errorCode,
            'error_message' => sprintf('curl_%d:%s', $errorCode, $errorMessage),
        ];
    }

    return ['status' => $status, 'error_code' => 0, 'error_message' => ''];
}

/**
 * @return array{status:int,error_code:int,error_message:string}
 */
function urlStatusCodeInsecure(string $url, int $originalErrorCode, string $originalErrorMessage): array
{
    $ch = curl_init($url);
    if ($ch === false) {
        return ['status' => 0, 'error_code' => $originalErrorCode, 'error_message' => sprintf('curl_%d:%s', $originalErrorCode, $originalErrorMessage)];
    }

    curl_setopt_array($ch, [
        CURLOPT_NOBODY => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT => 'quiltsco-url-cleaner/1.0',
    ]);

    curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $errorCode = curl_errno($ch);
    $errorMessage = curl_error($ch);
    curl_close($ch);

    if ($errorCode !== 0) {
        return [
            'status' => 0,
            'error_code' => $originalErrorCode,
            'error_message' => sprintf('curl_%d:%s', $originalErrorCode, $originalErrorMessage),
        ];
    }

    if ($status === 405) {
        return urlStatusCodeGetInsecure($url, $originalErrorCode, $originalErrorMessage);
    }

    return [
        'status' => $status,
        'error_code' => 0,
        'error_message' => sprintf('ssl_verification_failed_then_insecure_ok:curl_%d:%s', $originalErrorCode, $originalErrorMessage),
    ];
}

/**
 * @return array{status:int,error_code:int,error_message:string}
 */
function urlStatusCodeGetInsecure(string $url, int $originalErrorCode, string $originalErrorMessage): array
{
    $ch = curl_init($url);
    if ($ch === false) {
        return ['status' => 0, 'error_code' => $originalErrorCode, 'error_message' => sprintf('curl_%d:%s', $originalErrorCode, $originalErrorMessage)];
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT => 'quiltsco-url-cleaner/1.0',
        CURLOPT_RANGE => '0-0',
    ]);

    curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $errorCode = curl_errno($ch);
    curl_error($ch);
    curl_close($ch);

    if ($errorCode !== 0) {
        return [
            'status' => 0,
            'error_code' => $originalErrorCode,
            'error_message' => sprintf('curl_%d:%s', $originalErrorCode, $originalErrorMessage),
        ];
    }

    return [
        'status' => $status,
        'error_code' => 0,
        'error_message' => sprintf('ssl_verification_failed_then_insecure_ok:curl_%d:%s', $originalErrorCode, $originalErrorMessage),
    ];
}

function isSslRelatedCurlError(int $errorCode): bool
{
    return in_array($errorCode, [35, 51, 53, 54, 58, 59, 60, 66, 77, 80, 82, 83, 90, 91], true);
}

function isDefinitiveCurlError(int $errorCode): bool
{
    return in_array($errorCode, [3, 6], true);
}
