<?php
    $_aim = post('aim');
    $_city = post('city');
    $_country = post('country');
    $_day = postInt('day');
    $_fullName = post('fullname');
    $_gender = postInt('gender');
    $_gtalk = post('gtalk');
    $_icq = post('icq');
    $_month = postInt('month');
    $_msn = post('msn');
    $_notification = postInt('notification');
    $_privacy = postInt('privacy');
    $_region = post('region');
    $_website = post('website');
    $_yahoo = post('yahoo');
    $_year = postInt('year');

    if ($_gender != 0 && $_gender != 1 && $_gender != 2)
    {
        $_gender = 0;
    }

    if (checkdate($_month, $_day, $_year))
    {
        $birthday = substr('000' . $_year, -4) . '-' . substr('0' . $_month, -2) . '-' . substr('0' . $_day, -2);
    }
    else
    {
        $existingProfile = (new \Databases\MembersExtras())->findByUserId($GLOBALS['auth']['id']);

        $birthday = '0000-00-00';
        if ($existingProfile && isset($existingProfile['birthday']) && is_string($existingProfile['birthday']))
        {
            $birthday = $existingProfile['birthday'];
        }
    }

    $longitude = $latitude = 0;
    $cityId = $regionId = $countryId = 0;
    $cityName = $regionName = $countryName = '';

    $geoCitiesRow = (new \Databases\GeoCities())->findById($_city);
    if ($geoCitiesRow)
    {
        $cityId = $geoCitiesRow['city_id'];
        $regionId = $geoCitiesRow['region_id'];
    }

    $geoRegionsRow = null;
    if ($regionId || $_region)
    {
        $geoRegionsRow = (new \Databases\GeoRegions())->findById($regionId ? $regionId : $_region);
    }

    if ($geoRegionsRow)
    {
        $countryId = $geoRegionsRow['country_id'];
        $regionId = $geoRegionsRow['region_id'];
    }

    $geoCountriesRow = null;
    if ($countryId || $_country)
    {
        $geoCountriesRow = (new \Databases\GeoCountries())->findById($countryId ? $countryId : $_country);
    }

    if ($geoCountriesRow)
    {
        $countryId = $geoCountriesRow['country_id'];
    }

    if ($cityId)
    {
        $geoCitiesRow = (new \Databases\GeoCities())->findById($cityId);
        if ($geoCitiesRow)
        {
            $cityName = $geoCitiesRow['city_name'];
            $longitude = $geoCitiesRow['longitude'];
            $latitude = $geoCitiesRow['latitude'];
        }
    }

    if ($regionId)
    {
        $geoRegionsRow = (new \Databases\GeoRegions())->findById($regionId);
        if ($geoRegionsRow)
        {
            $regionName = $geoRegionsRow['region_name'];
        }
    }

    if ($countryId)
    {
        $geoCountriesRow = (new \Databases\GeoCountries())->findById($countryId);
        if ($geoCountriesRow)
        {
            $countryName = $geoCountriesRow['country_name'];
        }
    }


    $_website = trim($_website);
    if (strpos(strtolower($_website), 'http://') === 0)
    {
        $_website = 'https://' . substr($_website, 7);
    }
    (new \Databases\MembersExtras())->upsertProfile(
        $GLOBALS['auth']['id'],
        $_fullName,
        $birthday,
        $_gender,
        $countryName,
        $regionName,
        $cityName,
        $latitude,
        $longitude,
        $_website,
        $_aim,
        $_icq,
        $_msn,
        $_yahoo,
        $_gtalk,
        $_privacy,
        $_notification
    );
    header('Location: /?s=userinfo');
