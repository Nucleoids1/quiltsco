<?php

declare(strict_types=1);

namespace {
    require_once __DIR__ . '/_functions_test_helpers.php';

    final class UserinfoActionTestState
    {
        public static array $request = [];
    }

    function post(string $key): string
    {
        return (string) (UserinfoActionTestState::$request[$key] ?? '');
    }

    function postInt(string $key): int
    {
        return (int) (UserinfoActionTestState::$request[$key] ?? 0);
    }
}

namespace Databases {
    class GeoCities
    {
        public static array $rows = [];

        public function findById(int|string $cityId): ?array
        {
            return self::$rows[(int) $cityId] ?? null;
        }
    }

    class GeoRegions
    {
        public static array $rows = [];

        public function findById(int|string $regionId): ?array
        {
            return self::$rows[(int) $regionId] ?? null;
        }
    }

    class GeoCountries
    {
        public static array $rows = [];

        public function findById(int|string $countryId): ?array
        {
            return self::$rows[(int) $countryId] ?? null;
        }
    }

    class MembersExtras
    {
        public static array $lastProfile = [];
        public static ?array $existingProfile = null;

        public function findByUserId(int $memberId): ?array
        {
            return self::$existingProfile;
        }

        public function upsertProfile(
            int $memberId,
            string $fullName,
            string $birthday,
            int $gender,
            string $country,
            string $region,
            string $city,
            float|int $latitude,
            float|int $longitude,
            string $website,
            string $aim,
            string $icq,
            string $msn,
            string $yahoo,
            string $gtalk,
            int $privacy,
            int $notification
        ): void {
            self::$lastProfile = [
                'member_id' => $memberId,
                'birthday' => $birthday,
                'country' => $country,
                'region' => $region,
                'city' => $city,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];
        }
    }
}

namespace {
    use Databases\GeoCities;
    use Databases\GeoCountries;
    use Databases\GeoRegions;
    use Databases\MembersExtras;

    $GLOBALS['auth'] = ['id' => 123];

    GeoCities::$rows = [
        100 => ['city_id' => 100, 'region_id' => 10, 'city_name' => 'Seattle', 'latitude' => 47.6062, 'longitude' => -122.3321],
    ];
    GeoRegions::$rows = [
        10 => ['region_id' => 10, 'country_id' => 1, 'region_name' => 'Washington'],
        20 => ['region_id' => 20, 'country_id' => 2, 'region_name' => 'Ontario'],
    ];
    GeoCountries::$rows = [
        1 => ['country_id' => 1, 'country_name' => 'United States'],
        2 => ['country_id' => 2, 'country_name' => 'Canada'],
        3 => ['country_id' => 3, 'country_name' => 'Mexico'],
    ];

    $runCase = static function (array $request): array {
        UserinfoActionTestState::$request = $request;
        MembersExtras::$lastProfile = [];
        require dirname(__DIR__) . '/action/userinfo.php';

        return MembersExtras::$lastProfile;
    };

    $cityProfile = $runCase(['city' => 100]);
    assertSameValue('United States', $cityProfile['country'], 'City case derives country from selected city.');
    assertSameValue('Washington', $cityProfile['region'], 'City case derives region from selected city.');
    assertSameValue('Seattle', $cityProfile['city'], 'City case persists selected city name.');

    $regionProfile = $runCase(['region' => 20]);
    assertSameValue('Canada', $regionProfile['country'], 'Region-only case derives country from selected region.');
    assertSameValue('Ontario', $regionProfile['region'], 'Region-only case persists selected region name.');
    assertSameValue('', $regionProfile['city'], 'Region-only case leaves city empty.');

    $countryProfile = $runCase(['country' => 3]);
    assertSameValue('Mexico', $countryProfile['country'], 'Country-only case persists selected country name.');
    assertSameValue('', $countryProfile['region'], 'Country-only case leaves region empty.');
    assertSameValue('', $countryProfile['city'], 'Country-only case leaves city empty.');

    $invalidRegionProfile = $runCase(['region' => 999, 'country' => 2]);
    assertSameValue('Canada', $invalidRegionProfile['country'], 'Invalid region with valid country still persists selected country.');
    assertSameValue('', $invalidRegionProfile['region'], 'Invalid region does not leak stale region values.');
    assertSameValue('', $invalidRegionProfile['city'], 'Invalid region case keeps city empty.');

    MembersExtras::$existingProfile = ['birthday' => '1988-07-09'];
    $invalidBirthdayProfile = $runCase(['day' => 31, 'month' => 2, 'year' => 1990]);
    assertSameValue('1988-07-09', $invalidBirthdayProfile['birthday'], 'Invalid birthday does not overwrite existing birthday.');

    MembersExtras::$existingProfile = ['birthday' => '1988-07-09'];
    $validBirthdayProfile = $runCase(['day' => 10, 'month' => 3, 'year' => 1991]);
    assertSameValue('1991-03-10', $validBirthdayProfile['birthday'], 'Valid birthday updates saved birthday.');

    finishTest('action_userinfo_location_resolution.php');
}
