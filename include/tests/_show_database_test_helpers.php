<?php

declare(strict_types=1);

require_once __DIR__ . '/_functions_test_helpers.php';

/**
 * @return array<string, int>
 */
function extractShowDatabaseCalls(string $contents): array
{
    $matches = [];
    preg_match_all('/\(new\s+\\\Databases\\\([A-Za-z0-9_]+)\s*\(\)\)->([A-Za-z0-9_]+)\s*\(/', $contents, $matches, PREG_SET_ORDER);

    $calls = [];
    foreach ($matches as $match) {
        $signature = $match[1] . '::' . $match[2];
        if (!isset($calls[$signature])) {
            $calls[$signature] = 0;
        }

        $calls[$signature]++;
    }

    ksort($calls);
    return $calls;
}

/**
 * @param array<string,int> $expectedCalls
 */
function assertShowDatabaseFileContract(string $fileName, array $expectedCalls): void
{
    $path = dirname(__DIR__) . '/show/' . $fileName;

    assertTrue(is_file($path), $fileName . ' exists in include/show/.');
    if (!is_file($path)) {
        return;
    }

    $lintOutput = [];
    $lintExitCode = 0;
    exec('php -l ' . escapeshellarg($path), $lintOutput, $lintExitCode);
    assertSameValue(0, $lintExitCode, $fileName . ' passes php -l syntax check.');

    $contents = file_get_contents($path);
    assertTrue($contents !== false, $fileName . ' is readable.');
    if ($contents === false) {
        return;
    }

    ksort($expectedCalls);
    $actualCalls = extractShowDatabaseCalls($contents);

    $callAliases = [
        'Community::findById' => 'Community::selectPrimaryKey',
        'CommunityMessagesBodies::findById' => 'CommunityMessagesBodies::selectPrimaryKey',
        'CommunityPermissions::selectAdministratorCommunitiesByUser' => 'CommunityPermissions::sqlRead',
        'CommunityThreads::incrementViews' => 'CommunityThreads::updateWhere',
        'GalleryImages::countByUserId' => 'GalleryImages::count',
        'GalleryImages::findPageByUserIdPostedOn' => 'GalleryImages::selectWhere',
        'Members::findById' => 'Members::selectPrimaryKey',
        'MembersCreate::findByUserId' => 'MembersCreate::selectWhereRow',
        'MembersExtras::findByUserId' => 'MembersExtras::selectWhereRow',
        'MembersMoods::findById' => 'MembersMoods::selectPrimaryKey',
        'Quilts::findById' => 'Quilts::selectPrimaryKey',
        'SecurityCodeCache::replaceForCache' => 'SecurityCodeCache::sqlWrite',
        'SecurityCodeLast::replaceForUser' => 'SecurityCodeLast::sqlWrite',
        'Tiles::findById' => 'Tiles::selectPrimaryKey',
        'TrackerBugs::countAll' => 'TrackerBugs::count',
        'TrackerBugs::countFromId' => 'TrackerBugs::count',
        'TrackerBugs::findById' => 'TrackerBugs::selectPrimaryKey',
        'TrackerBugs::incrementViews' => 'TrackerBugs::sqlWrite',
        'TrackerBugsCategories::findById' => 'TrackerBugsCategories::selectPrimaryKey',
        'TrackerBugsStatus::findById' => 'TrackerBugsStatus::selectPrimaryKey',
    ];

    $normalizedCalls = [];
    foreach ($actualCalls as $signature => $count) {
        $normalizedSignature = $callAliases[$signature] ?? $signature;
        if (!isset($normalizedCalls[$normalizedSignature])) {
            $normalizedCalls[$normalizedSignature] = 0;
        }

        $normalizedCalls[$normalizedSignature] += $count;
    }

    $expectedByClass = [];
    foreach ($expectedCalls as $signature => $expectedCount) {
        [$className] = explode('::', $signature, 2);
        if (!isset($expectedByClass[$className])) {
            $expectedByClass[$className] = 0;
        }

        $expectedByClass[$className] += $expectedCount;
    }

    $actualByClass = [];
    foreach ($normalizedCalls as $signature => $actualCount) {
        [$className] = explode('::', $signature, 2);
        if (!isset($actualByClass[$className])) {
            $actualByClass[$className] = 0;
        }

        $actualByClass[$className] += $actualCount;
    }

    $missingOrUnderCount = [];
    foreach ($expectedByClass as $className => $expectedCount) {
        $actualCount = $actualByClass[$className] ?? 0;
        if ($actualCount < $expectedCount) {
            $missingOrUnderCount[$className] = ['expected' => $expectedCount, 'actual' => $actualCount];
        }
    }

    assertSameValue([], $missingOrUnderCount, $fileName . ' keeps expected database class-level coverage guarantees.');
}
