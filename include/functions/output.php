<?php
function boxOutsideTop($title = '', $options = '', $width = '', $styleTitle = '', $styleOptions = '', $styleMain = '', $id = '')
{
    $defaultTitle = '';
    $defaultOptions = '';
    $defaultMain = '';
    if ($width == '') {
        if ($options) {
            $width = '50%';
        } else {
            $width = '30%';
        }
    }
    $widthStyle = is_numeric($width) ? $width . 'px' : $width;
    $return = '<div class="header">';
    $return .= '<table role="presentation" style="width: 100%; border-spacing: 0;"><tr>';
    $return .= '<td style="vertical-align: top;"><div' . ($id ? ' id="title_' . $id . '"' : '') . ' class="outside_title" style="' . overwriteStyle($defaultTitle, $styleTitle) . '">' . $title . '</div></td>';
    $return .= '<td style="width: ' . $widthStyle . '; vertical-align: top; text-align: right;"><div' . ($id ? ' id="options_' . $id . '"' : '') . ' class="outside_options" style="' . overwriteStyle($defaultOptions, $styleOptions) . '">' . $options . '</div></td>';
    $return .= '</tr></table>';
    $return .= '</div>';
    $return .= '<div' . ($id ? ' id="main_' . $id . '"' : '') . ' class="outside_main" style="' . overwriteStyle($defaultMain, $styleMain) . '">';
    return $return;
}

function boxOutsideBottom()
{
    return '</div>' . "\r\n";
}

function boxInsideTop($style = '', $id = '')
{
    $default = '';
    return '<div' . ($id ? ' id="inside_' . $id . '"' : '') . ' class="inside" style="' . overwriteStyle($default, $style) . '">' . "\r\n";
}

function boxInsideBottom()
{
    return '</div>' . "\r\n";
}

function boxStyle($style = '')
{
    $default = 'background: #' . $GLOBALS['color']['inside_background'] . '; border-color: #' . $GLOBALS['color']['inside_border'] . '; border-style: solid; border-width: 2px; color: #' . $GLOBALS['color']['inside_text'] . '; padding: 5px; border-radius: 0px 15px 15px 15px;';
    return overwriteStyle($default, $style) . '" class="inside_main';
}

function boxImageTop($style = '', $mouseover = 1, $id = '')
{
    $default = '';
    return '<div' . ($id ? ' id="image_' . $id . '"' : '') . ' class="image" style="' . overwriteStyle($default, $style) . '"' . ($mouseover ? ' onMouseOver="this.className=\'image_hover\';" onMouseOut="this.className=\'image\'"' : '') . '>' . "\r\n";
}

function boxImageBottom()
{
    return '</div>' . "\r\n";
}

function makePages($title, $width = '', $style = '')
{
    $default = '';
    if ($width == '') {
        $width = '100%';
    }
    $widthStyle = is_numeric($width) ? $width . 'px' : $width;
    return '<table role="presentation" style="width: ' . $widthStyle . '; border-spacing: 0;"><tr><td style="vertical-align: top;"><div class="header" style="' . overwriteStyle($default, $style) . '">' . $title . '</div></td></tr></table>' . "\r\n";
}

function overwriteStyle($default, $overwrite)
{
    if ($overwrite) {
        $explode = explode(';', $overwrite);
        foreach ($explode as $value) {
            if ($value = trim($value)) {
                if (strpos($value, ':') !== false) {
                    $pos1 = strpos($default, substr($value, 0, strpos($value, ':')));
                    $pos2 = strpos($default, ';', $pos1);
                    if ($pos1 !== false && $pos2 !== false) {
                        $default = substr($default, 0, $pos1) . $value . substr($default, $pos2);
                    } else {
                        $default .= ' ' . $value . ';';
                    }
                }
            }
        }
    }
    return $default;
}

function makeLink($actionUrl = '', $title = '', $class = '', $onclick = '')
{
    return '<a href="' . $actionUrl . '"'
        . ($class ? ' class="' . $class . '"' : '')
        . ($onclick ? ' onClick="' . $onclick . '"' : '')
        . '>' . $title . '</a>';
}

function makeButton($title = '', $href = '', $id = '', $div = '')
{
    return '<div class="' . $id . '" style="border-width: 2px; border-style: solid; cursor: pointer; font-size: 1.05rem; font-weight: bold; padding: 7px 15px 7px 15px; text-align: center; border-radius: 17px 17px 17px 17px;" onClick="document.location=\'' . $href . '\';" onMouseOver="this.className=\'' . $id . '_hover\';' . ($div ? ' if (undefined !== window.startme) { show_div(\'' . $div . '\') };' : '') . '" onMouseOut="this.className=\'' . $id . '\';">' . $title . '</div>';
}

function makePostLink($actionUrl, $title, $confirmMessage = '')
{
    $confirmJs = $confirmMessage ? 'if (!confirm(' . safeJs($confirmMessage) . ')) { return false; } ' : '';
    $style = $buttonStyle ? $buttonStyle : 'text-decoration: underline; cursor: pointer;';
    $onclick = $confirmJs . 'this.closest(\'form\').submit(); return false;';
    return '<form action="' . safeUrl($actionUrl) . '" method="post" style="display: inline; margin: 0;">'
        . csrfField()
        . '<a href="' . safeUrl($actionUrl) . '" style="' . safeAttr($style) . '"' . $classAttr . ' onClick="' . $onclick . '">' . $title . '</a>'
        . '</form>';
}
