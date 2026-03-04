<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/horoscope.php';
require_once __DIR__ . '/_functions_test_helpers.php';

assertFunctionFileContract('horoscope.php', ['getHoroscopeSign']);

assertSameValue('Capricorn', getHoroscopeSign('2000-01-19'), 'Capricorn boundary day is correct.');
assertSameValue('Aquarius', getHoroscopeSign('2000-01-20'), 'Aquarius starts Jan 20.');
assertSameValue('Pisces', getHoroscopeSign('2000-03-20'), 'Pisces boundary day is correct.');
assertSameValue('Aries', getHoroscopeSign('2000-03-21'), 'Aries starts Mar 21.');
assertSameValue('Sagittarius', getHoroscopeSign('2000-12-01'), 'December 1 maps to Sagittarius.');
assertSameValue('Capricorn', getHoroscopeSign('2000-12-31'), 'Late December maps to Capricorn.');

finishTest('functions_horoscope.php');
