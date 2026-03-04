<?php

declare(strict_types=1);

require_once __DIR__ . '/../functions/output.php';
require_once __DIR__ . '/_functions_test_helpers.php';

if (!function_exists('safeUrl')) {
    function safeUrl($value)
    {
        return $value;
    }
}
if (!function_exists('safeAttr')) {
    function safeAttr($value)
    {
        return $value;
    }
}
if (!function_exists('safeJs')) {
    function safeJs($value)
    {
        return "'" . str_replace("'", "\\'", $value) . "'";
    }
}
if (!function_exists('csrfField')) {
    function csrfField()
    {
        return '';
    }
}


assertFunctionFileContract('output.php', ['boxOutsideTop', 'boxOutsideBottom', 'boxInsideTop', 'boxInsideBottom', 'boxStyle', 'boxImageTop', 'boxImageBottom', 'makePages', 'overwriteStyle', 'makeButton', 'makeLink', 'makePostLink']);

$link = makeLink('/home', 'Home', 'menu_link', 'return false;');
assertContains('href="/home"', $link, 'makeLink includes href.');
assertContains('class="menu_link"', $link, 'makeLink includes class.');
assertContains('onClick="return false;"', $link, 'makeLink includes optional onclick when provided.');
assertContains('onMouseOver="this.className=\'menu_link_hover\';"', $link, 'makeLink includes hover handler when class is provided.');

$linkWithoutClass = makeLink('/home', 'Home');
assertContains('href="/home"', $linkWithoutClass, 'makeLink includes href when class is omitted.');
assertFalse(strpos($linkWithoutClass, 'class=""') !== false, 'makeLink omits empty class attribute when class is not provided.');
assertFalse(strpos($linkWithoutClass, 'onMouseOver=') !== false, 'makeLink omits onMouseOver when class is not provided.');
assertFalse(strpos($linkWithoutClass, 'onMouseOut=') !== false, 'makeLink omits onMouseOut when class is not provided.');

$button = makeButton('Go', '/go', 'btn_primary', 'panelA');
assertContains("document.location='/go';", $button, 'makeButton includes click location.');
assertContains("show_div('panelA')", $button, 'makeButton includes show_div hook when provided.');

$postLink = makePostLink('?a=delete&i=1', 'Delete');
assertContains('<a href="?a=delete&i=1"', $postLink, 'makePostLink renders an anchor element.');
assertContains('this.closest(\'form\').submit(); return false;', $postLink, 'makePostLink anchor submits enclosing form.');
assertContains('class="action_post_link"', $postLink, 'makePostLink includes default class for hover styling.');

$confirmPostLink = makePostLink('?a=delete&i=1', 'Delete', 'Delete this record?');
assertContains('if (!confirm(\'Delete this record?\')) { return false; }', $confirmPostLink, 'makePostLink includes confirm guard when provided.');

$styledPostLink = makePostLink('?a=delete&i=1', 'Delete', '', 'color:red;');
assertFalse(strpos($styledPostLink, 'class="action_post_link"') !== false, 'makePostLink does not force class when custom style is provided.');

$outside = boxOutsideTop('Title', '<a href="#">opt</a>', '55%', '', '', '', 'sample');
assertContains('id="title_sample"', $outside, 'boxOutsideTop includes title id when id provided.');
assertContains('id="options_sample"', $outside, 'boxOutsideTop includes options id when id provided.');
assertContains('outside_options_link_hover', $outside, 'boxOutsideTop assigns dedicated hover class to option links.');

assertSameValue('color:red; font-size: 1.0rem; padding:3px;', overwriteStyle('color:red; font-size: 0.9rem;', 'font-size: 1.0rem; padding:3px;'), 'overwriteStyle replaces and appends style declarations.');
assertSameValue("</div>\r\n", boxInsideBottom(), 'boxInsideBottom returns closing HTML fragment.');
assertSameValue("</div>\r\n", boxOutsideBottom(), 'boxOutsideBottom returns closing HTML fragment.');
assertSameValue("</div>\r\n", boxImageBottom(), 'boxImageBottom returns closing HTML fragment.');

finishTest('functions_output.php');
