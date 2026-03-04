<?php
function getHoroscopeSign($dob)
{
    $month = substr($dob, 5, 2);
    $day = substr($dob, 8, 2);
    $sign = '';
    if ($month == 1 && $day >= 20) $sign = "Aquarius";
    if ($month == 2 && $day <= 18) $sign = "Aquarius";
    if ($month == 2 && $day >= 19) $sign = "Pisces";
    if ($month == 3 && $day <= 20) $sign = "Pisces";
    if ($month == 3 && $day >= 21) $sign = "Aries";
    if ($month == 4 && $day <= 19) $sign = "Aries";
    if ($month == 4 && $day >= 20) $sign = "Taurus";
    if ($month == 5 && $day <= 20) $sign = "Taurus";
    if ($month == 5 && $day >= 21) $sign = "Gemini";
    if ($month == 6 && $day <= 21) $sign = "Gemini";
    if ($month == 6 && $day >= 22) $sign = "Cancer";
    if ($month == 7 && $day <= 22) $sign = "Cancer";
    if ($month == 7 && $day >= 23) $sign = "Leo";
    if ($month == 8 && $day <= 22) $sign = "Leo";
    if ($month == 8 && $day >= 23) $sign = "Virgo";
    if ($month == 9 && $day <= 22) $sign = "Virgo";
    if ($month == 9 && $day >= 23) $sign = "Libra";
    if ($month == 10 && $day <= 22) $sign = "Libra";
    if ($month == 10 && $day >= 23) $sign = "Scorpio";
    if ($month == 11 && $day <= 21) $sign = "Scorpio";
    if ($month == 11 && $day >= 22) $sign = "Sagittarius";
    if ($month == 12 && $day <= 21) $sign = "Sagittarius";
    if ($month == 12 && $day >= 22) $sign = "Capricorn";
    if ($month == 1 && $day <= 19) $sign = "Capricorn";
    return $sign;
}
