<?php
    $_id = getInt('i');
    $_page = getInt('p', 1);

    $content = '';
    switch($_page)
    {
        case '1':
            $content .= '<select class="input_select" name="region" id="region" onChange="updateCity(document.getElementById(\'region\').value);">';
            $geoRegionsRows = (new \Databases\GeoRegions())->findByCountryId($_id);
            if (count($geoRegionsRows))
            {
                foreach ($geoRegionsRows as $geoRegionsRow)
                {
                    $content .= '<option value="' . $geoRegionsRow['region_id'] . '">' . $geoRegionsRow['region_name'] . '</option>';
                }
            }
            else
            {
                $content .= '<option value="0">No Regions Available</option>';
            }
            $content .= '</select>';
            break;
        case '2':
            $content .= '<select class="input_select" name="city" id="city">';
            $geoCitiesRows = (new \Databases\GeoCities())->findByRegionId($_id);
            if (count($geoCitiesRows))
            {
                foreach ($geoCitiesRows as $geoCitiesRow)
                {
                    $content .= '<option value="' . $geoCitiesRow['city_id'] . '">' . $geoCitiesRow['city_name'] . '</option>';
                }
            }
            else
            {
                $content .= '<option value="0">No Cities Available</option>';
            }
            $content .= '</select>';
            break;
    }

    print preg_replace("/[\r\n]/", " \\n\\\n", $content);
