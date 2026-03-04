<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="<?php echo language() == 'french' ? 'fr' : 'en'; ?>">
<head>
<meta name="verify-v1" content="5N9IQ+Rztg6+uhSHy3OKNEW1gBEmNurJL3pVZpEiz/A=" />
<title>Quilts Community</title>
<style>
body
{
    background: #303538;
    color: #cccccc;
    font-family: 'Verdana', 'Geneva', 'sans-serif';
    font-size: 1.0rem;
    margin: 0;
    padding: 0;
}
h1.header, h2.header
{
    display: block;
    margin: 0 0 5px 0;
}
.hidden
{
    border: 0;
    clip: rect(0, 0, 0, 0);
    height: 1px;
    margin: -1px;
    overflow: hidden;
    padding: 0;
    position: absolute;
    white-space: nowrap;
    width: 1px;
}
input:-webkit-autofill, 
textarea:-webkit-autofill, 
select:-webkit-autofill { 
    box-shadow: 0 0 0 1000px #0b0f12 inset !important; 
    -webkit-box-shadow: 0 0 0 1000px #0b0f12 inset !important; 
    -webkit-text-fill-color: #ffb36b !important; 
    transition: background-color 9999s ease-in-out 0s;
    
}
input:-webkit-autofill::first-line,
textarea:-webkit-autofill::first-line,
select:-webkit-autofill::first-line
{
  font-size: 0.8125rem !important;
}
td, tr
{
    font-family: 'Verdana', 'Geneva', 'sans-serif';
    font-size: 1.0rem;
}
.form_label_cell
{
    width: 200px;
    text-align: right;
    padding: 4px 6px;
}
.form_input_cell
{
    padding: 4px 6px;
}
.content, .outside_main
{
    padding: 5px;
    color: #cccccc;
    border: 2px #282c2f solid;
    margin-bottom: 5px;
    border-radius: 8px;
}
.content a,
.content a:visited
{
    color: #9be9ff;
    text-decoration: underline;
    text-decoration-color: rgba(155, 233, 255, 0.6);
    text-underline-offset: 2px;
    text-decoration-thickness: 1px;
    text-shadow: 0 0 5px rgba(155, 233, 255, 0.18);
    transition: color 0.2s ease, text-decoration-color 0.2s ease, text-shadow 0.2s ease;
}
.content a:hover,
.content a:active,
.content a:focus
{
    color: #ffd18a;
    outline: none;
    text-decoration-color: rgba(255, 209, 138, 0.9);
    text-shadow: 0 0 8px rgba(255, 209, 138, 0.45);
}
a:has(img)
{
    border-bottom: none !important;
    text-decoration: none !important;
}
.fixup_fill
{
    padding: 1px;
}
.form
{
    margin: 0px;
    display: inline;
}
.header
{
    padding: 5px 15px 5px 15px;
    background-color: #282c2f;
    color: #c7ff70;
    font-size: 1.05rem;
    font-weight: bold;
    border: 1px #282c2f solid;
    border-radius: 8px;
    margin-bottom: 5px;
}
.header td
{
    color: #c7ff70;
    font-size: 1.05rem;
    font-weight: bold;
}
.header a,
.header a:visited
{
    border-bottom: 1px solid rgba(155, 233, 255, 0.35);
    color: #9be9ff;
    display: inline-block;
    padding: 0 2px;
    text-decoration: none;
    text-shadow: 0 0 6px rgba(155, 233, 255, 0.25);
    transition: border-color 0.2s ease, color 0.2s ease, text-shadow 0.2s ease;
}
.header a:hover,
.header a:active,
.header a:focus
{
    border-bottom-color: rgba(255, 209, 138, 0.85);
    color: #ffd18a;
    outline: none;
    text-shadow: 0 0 8px rgba(255, 209, 138, 0.45);
}
.footer
{
    background: #1f2427;
    border: 1px solid #282c2f;
    border-radius: 8px;
    color: #aeb9bf;
    margin: 4px 0 6px;
    padding: 6px 10px;
}
.footer a,
.footer a:visited
{
    border-bottom: 1px solid rgba(196, 210, 220, 0.45);
    color: #d0dee8;
    text-decoration: none;
    transition: border-color 0.2s ease, color 0.2s ease, text-shadow 0.2s ease;
}
.footer a:hover,
.footer a:active,
.footer a:focus
{
    border-bottom-color: rgba(143, 243, 255, 0.9);
    color: #8ff3ff;
    outline: none;
    text-shadow: 0 0 8px rgba(143, 243, 255, 0.4);
}
.quilt_tiles
{
    margin: 0;
    padding: 0;
    background: transparent;
    border: 0;
    border-radius: 0;
    overflow: auto;
}
.quilt_tiles .quilt-tiles-grid
{
    margin: 0 auto;
}
.quilt_tiles td
{
    color: #d9e7ee;
    font-size: 1.0rem;
    padding: 0;
    line-height: 0;
}
.quilt_tiles a,
.quilt_tiles a:visited
{
    color: #7ee6ff;
    text-decoration: underline;
    text-decoration-color: rgba(126, 230, 255, 0.7);
    text-underline-offset: 2px;
    text-decoration-thickness: 1px;
    text-shadow: 0 0 6px rgba(126, 230, 255, 0.2);
}
.quilt_tiles a:hover,
.quilt_tiles a:active,
.quilt_tiles a:focus
{
    color: #ffd596;
    text-decoration-color: rgba(255, 213, 150, 0.95);
    text-shadow: 0 0 10px rgba(255, 213, 150, 0.45);
}
.image
{
    background: #303538;
    border: 2px solid #282c2f;
    color: #FFFFFF;
    float: left;
    margin: 5px 5px 0 0;
    padding: 5px;
    text-align: center;
    font-size: 0.9rem;
    border-radius: 8px;
}
.image a:link, .image a:visited
{
    color: #FF7777;
    text-decoration: none;
}
.image a:hover, .image a:active
{
    color: #FF9900;
    text-decoration: none;
}
.image_hover
{
    background: #404548;
    border: 2px solid #FF9900;
    color: #FFFFFF;
    float: left;
    margin: 5px 5px 0 0;
    padding: 5px;
    text-align: center;
    font-size: 0.9rem;
    border-radius: 8px;
}
.image_hover a:link, .image_hover a:visited
{
    color: #FF7777;
    text-decoration: none;
}
.image_hover a:hover, .image_hover a:active
{
    color: #FF9900;
    text-decoration: none;
}
.image_link
{
    color: #FF7777;
    text-decoration: none;
}
.image_link_hover
{
    color: #FF9900;
    text-decoration: none;
}
.input_select
{
    background-color: #0b0f12;
    border: 1px solid #31d8ff;
    border-radius: 6px;
    color: #ffb36b;
    -webkit-text-fill-color: #ffb36b;
    caret-color: #ffb36b;
    font-size: 1.0rem;
    line-height: 1.35;
    min-height: 30px;
    padding: 4px 34px 4px 10px;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05), 0 1px 3px rgba(0, 0, 0, 0.3);
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    appearance: none;
    background-image: linear-gradient(45deg, transparent 50%, #31d8ff 50%), linear-gradient(135deg, #31d8ff 50%, transparent 50%);
    background-position: calc(100% - 17px) calc(50% - 2px), calc(100% - 11px) calc(50% - 2px);
    background-size: 6px 6px, 6px 6px;
    background-repeat: no-repeat;
    box-sizing: border-box;
    height: 30px;
}
.input_select:active
{
    outline: 2px solid #31d8ff;
    outline-offset: 1px;
    border-color: #65e6ff;
    box-shadow: 0 0 0 2px rgba(49, 216, 255, 0.25), 0 0 8px rgba(49, 216, 255, 0.5);
}
.input_select:focus,
.input_select:focus-visible
{
    outline: 2px solid #31d8ff;
    outline-offset: 1px;
    border-color: #65e6ff;
    box-shadow: 0 0 0 2px rgba(49, 216, 255, 0.25), 0 0 8px rgba(49, 216, 255, 0.5);
}
.input_select:hover
{
    border-color: #65e6ff;
    background-color: #10171c;
}
.input_submit
{
    background: linear-gradient(180deg, #1a252d 0%, #0d1419 100%);
    border: 1px solid #2ed8ff;
    border-radius: 8px;
    box-shadow: 0 2px 0 rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.1);
    color: #8ff3ff;
    cursor: pointer;
    font-weight: bold;
    letter-spacing: 0.2px;
    min-height: 32px;
    padding: 6px 14px;
    text-shadow: 0 0 5px rgba(46, 216, 255, 0.35);
    transition: background 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, color 0.2s ease, transform 0.05s ease;
}
.input_submit:active
{
    transform: translateY(1px);
    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.08);
}
.input_submit:disabled
{
    opacity: 0.6;
    cursor: not-allowed;
    box-shadow: none;
}
.input_submit:focus,
.input_submit:focus-visible
{
    outline: 2px solid #31d8ff;
    outline-offset: 1px;
    border-color: #65e6ff;
    box-shadow: 0 0 0 2px rgba(49, 216, 255, 0.25), 0 0 8px rgba(49, 216, 255, 0.5);
}
.input_submit:hover
{
    background: linear-gradient(180deg, #233541 0%, #13232d 100%);
    border-color: #72ebff;
    box-shadow: 0 0 0 2px rgba(49, 216, 255, 0.15), 0 3px 10px rgba(49, 216, 255, 0.18);
    color: #d9fbff;
}
.input_text
{
    background-color: #0b0f12;
    border: 1px solid #31d8ff;
    border-radius: 6px;
    box-sizing: border-box;
    color: #ffb36b;
    font-size: 1.0rem;
    height: 30px;
    line-height: 1.35;
    padding: 4px 10px;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05), 0 1px 3px rgba(0, 0, 0, 0.3);
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
}
.input_text:focus,
.input_text:focus-visible
{
    outline: 2px solid #31d8ff;
    outline-offset: 1px;
    border-color: #65e6ff;
    box-shadow: 0 0 0 2px rgba(49, 216, 255, 0.25), 0 0 8px rgba(49, 216, 255, 0.5);

}
.inside
{
    background: #303538;
    border-color: #282c2f;
    border-style: solid;
    border-width: 2px;
    color: #FFFFFF;
    font-size: 1.0rem;
    padding: 8px;
    border-radius: 0px 8px 8px 8px;
}
.inside a
{
    color: #78c8ff;
    text-decoration: underline;
    text-decoration-color: rgba(120, 200, 255, 0.6);
    text-underline-offset: 2px;
    transition: color 0.2s ease, text-decoration-color 0.2s ease, text-shadow 0.2s ease;
}
.inside a:visited
{
    color: #78c8ff;
    text-decoration-color: rgba(120, 200, 255, 0.6);
}
.inside a:hover
{
    color: #c5e9ff;
    text-decoration-color: rgba(197, 233, 255, 0.95);
    text-shadow: 0 0 8px rgba(120, 200, 255, 0.45);
}
.inside a:focus,
.inside a:focus-visible
{
    outline: 2px solid #31d8ff;
    outline-offset: 2px;
    border-radius: 2px;
}
.main_navigation
{
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 6px;
}
.main_navigation li
{
    margin: 0;
    padding: 0;
    text-align: center;
    font-weight: bold;
}
.main_navigation li a,
.main_navigation li a:visited
{
    display: block;
    background: #21262a;
    border: 3px solid #46535c;
    border-radius: 6px;
    color: #bceeff;
    padding: 14px 16px;
    font-size: 1.0rem;
    line-height: 1.2;
    text-decoration: none;
    text-shadow: 0 0 6px rgba(141, 232, 255, 0.2);
    transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
}
.main_navigation li a:hover,
.main_navigation li a:active
{
    background: #283037;
    border-color: #6f8794;
    color: #ffffff;
    box-shadow: 0 0 10px rgba(141, 232, 255, 0.2);
}
.main_navigation li a:focus,
.main_navigation li a:focus-visible
{
    outline: 2px solid #31d8ff;
    outline-offset: 2px;
    border-color: #7fd8f0;
    color: #ffffff;
}
.main_navigation li.active a,
.main_navigation li.active a:visited
{
    background: rgba(127, 232, 255, 0.15);
    border-color: #7fe8ff;
    color: #dff8ff;
    box-shadow: 0 0 8px rgba(127, 232, 255, 0.25);
}
.logo a
{
    display: inline-block;
    border: 0;
    line-height: 0px;
}
.logo a:focus,
.logo a:focus-visible
{
    outline: 2px solid #31d8ff;
    outline-offset: 3px;
    border-radius: 2px;
}
.logo a img
{
    border: 0;
}
.top a,
.top a:visited
{
    color: #9be9ff;
    text-decoration: none;
    border-bottom: 1px solid rgba(155, 233, 255, 0.45);
    padding: 1px 2px;
    text-shadow: 0 0 5px rgba(155, 233, 255, 0.25);
    transition: border-color 0.2s ease, color 0.2s ease, text-shadow 0.2s ease;
}
.top a:hover,
.top a:active
{
    color: #ffd18a;
    border-bottom-color: rgba(255, 209, 138, 0.9);
    text-shadow: 0 0 7px rgba(255, 209, 138, 0.45);
}
.top a:focus,
.top a:focus-visible
{
    color: #ffd18a;
    outline: 2px solid #31d8ff;
    outline-offset: 1px;
    border-radius: 2px;
    border-bottom-color: rgba(255, 209, 138, 0.9);
}
.major_fill
{
    margin: 10px;
    padding: 5px;
    background-color: #101316;
    border: 0px #000000 solid;
    border-radius: 8px;
}
.minor_fill
{
    padding: 5px 5px 0px 5px;
    background-color: #303538;
    color: #cccccc;
    border: 0px #000000 solid;
    border-radius: 8px;
}
.notice_attention
{
    color: #FFFF00;
    font-family: Verdana, Arial;
}
.notice_error
{
    color: #FF7777;
    font-family: Verdana, Arial;
}
.notice_good
{
    color: #00FF00;
    font-family: Verdana, Arial;
}
.pages
{
    color: #cccccc;
    font-size: 1.0rem;
}
.skip-link
{
    position: absolute;
    top: -40px;
    left: 0;
    padding: 8px;
    background: #000;
    color: #fff;
    z-index: 100;
}
.skip-link:focus
{
    top: 0;
}
</style>
<script src="javascript/jquery-3.7.1.min.js"></script>
<script>
var confirmMsg = 'Do you really want to';
function confirmLink(theLink, theReason) {
    if (confirmMsg == '')
    {
        return true;
    }
    var isConfirmed = confirm(confirmMsg + ':\n' + theReason);
    return isConfirmed;
}
var csrfToken = '<?php echo csrfGetToken(); ?>';

function ajaxPost(url, returnId)
{
    if (!returnId)
    {
        if (window.console && console.warn) {
            console.warn('ajaxPost requires a returnId');
        }
        return false;
    }
    jQuery.post(url, { csrf_token: csrfToken }, function(responseText) {
        document.getElementById(returnId).innerHTML = responseText;
    });
    return false;
}
function ajaxPostAppend(url, returnId, before, after, todo)
{
    jQuery.post(url, { csrf_token: csrfToken }, function(responseText) {
        if (todo == 'value') {
            document.getElementById(returnId).value = document.getElementById(returnId).value + before + responseText + after;
        } else {
            document.getElementById(returnId).innerHTML = document.getElementById(returnId).innerHTML + before + responseText + after;
        }
    });
    return false;
}
function serializeForm(formId)
{
    var form = document.getElementById(formId);
    if (!form)
    {
        return '';
    }
    var fields = [];
    for (var i = 0; i < form.elements.length; i++)
    {
        var el = form.elements[i];
        if (!el.name || el.disabled)
        {
            continue;
        }
        if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked)
        {
            continue;
        }
        fields.push(encodeURIComponent(el.name) + '=' + encodeURIComponent(el.value));
    }
    return fields.join('&');
}
function ajaxPostSerialization(url, formId, returnId)
{
    if (!returnId)
    {
        if (window.console && console.warn) {
            console.warn('ajaxPostSerialization requires a returnId');
        }
        return false;
    }
    var data = 'csrf_token=' + encodeURIComponent(csrfToken);
    var serialized = serializeForm(formId);
    if (serialized)
    {
        data += '&' + serialized;
    }
    jQuery.post(url, data, function(responseText) {
        document.getElementById(returnId).innerHTML = responseText;
    });
    return false;
}

function ajaxJson(url)
{
    jQuery.post(url, { csrf_token: csrfToken }, function(responseText) {
        try {
            var data = responseText;
            var response = (typeof data === 'string') ? JSON.parse(data) : data;
            if (response && response.actions && Array.isArray(response.actions)) {
                for (var i = 0; i < response.actions.length; i++) {
                    var action = response.actions[i];
                    var element = document.getElementById(action.id);
                    if (!element) {
                        continue;
                    }
                    if (action.action === 'update') {
                        element.innerHTML = action.content;
                    } else if (action.action === 'show') {
                        element.style.display = action.display || 'block';
                    } else if (action.action === 'hide') {
                        element.style.display = 'none';
                    } else if (action.action === 'value') {
                        element.value = action.content;
                    }
                }
            }
        }
        catch (e) {
            if (window.console && console.error) {
                console.error('AJAX JSON parse error:', e);
            }
        }
    });
    return false;
}

function toggle(itemName)
{
    var obj = document.getElementById(itemName);
    if (obj.style.display == 'none')
    {
        obj.style.display = 'block';
        obj.focus();
    }
    else
    {
        obj.style.display = 'none';
    }
}
function toggleInline(itemName)
{
    var obj = document.getElementById(itemName);
    if (obj.style.display == 'none')
    {
        obj.style.display = 'inline';
        obj.focus();
    }
    else
    {
        obj.style.display = 'none';
    }
}
</script>
</head>
<body>
<?php echo makeLink('#main-content', 'Skip to main content', 'skip-link') ?>
<header role="banner">
<div class="top">
<table role="presentation" style="width: 100%; height: 35px; background-color: #101316; border-spacing: 0;"><tr>
<td style="text-align: left; padding-left: 20px; padding-right: 20px; padding-top: 2px; padding-bottom: 2px; white-space: nowrap;">
<?php
    if ($GLOBALS['auth']['id'])
    {
        $messagesCount = (new \Databases\Messages())->countUnreadByRecipientId($GLOBALS['auth']['id']);
        $headerLinks = '<b>' . $GLOBALS['auth']['username'] . '</b> [ ' . makeLink('?s=messages', $messagesCount . ' New Messages') . ' ]';
        if ((int) $GLOBALS['auth']['id'] === 1)
        {
            $headerLinks .= ' [ ' . makePostLink('?a=test_email&b=' . encodeUrlPath(server('QUERY_STRING')), 'Test Email') . ' ]';
        }
        $headerLinks .= ' [ ' . makePostLink('?a=logoff&b=' . encodeUrlPath(server('QUERY_STRING')), 'Logoff') . ' ]';
        echo $headerLinks;
    }
    else
    {
        echo '<b>Anonymous</b> [ ' . makeLink('?s=new', 'New Account') . ' ] [ ' . makeLink('?s=password', 'Forgot Password') . ' ]';
    }
?>
</td>
<td style="text-align: right; white-space: nowrap; padding-left: 10px; <?php echo (!$GLOBALS['auth']['id'] ? ' padding-right: 10px;' : '') ?>padding-top: 2px; padding-bottom: 2px;">
<?php
    if ($GLOBALS['auth']['id'])
    {
        $countTilesApprovals = (new \Databases\Tiles())->countPendingApprovalsByUser($GLOBALS['auth']['id']);
        $countTilesPending = (new \Databases\TilesPending())->countByUserId($GLOBALS['auth']['id']);
        $countTiles = (new \Databases\Tiles())->countByUserNotDeleted($GLOBALS['auth']['id']);
        $countGalleryImages = (new \Databases\GalleryImages())->countByUserId($GLOBALS['auth']['id']);
        $countQuiltsPermissions = (new \Databases\QuiltsPermissions())->countByUser($GLOBALS['auth']['id']);
        $countFriends = (new \Databases\Friends())->countByUserId($GLOBALS['auth']['id']);
        echo '<span style="padding-top: 11px; padding-bottom: 11px; padding-left: 20px; padding-right: 20px; background: ' . ($GLOBALS['highlight'] == 'approvals' ? '#202326' : '#101316') . ';"><b>Approvals:</b> ' . makeLink('?s=approvals', $countTilesApprovals) . '</span>';
        echo '<span style="padding-top: 11px; padding-bottom: 11px; padding-left: 20px; padding-right: 20px; background: ' . ($GLOBALS['highlight'] == 'tiles_pending' ? '#202326' : '#101316') . ';"><b>Tiles Pending:</b> ' . makeLink('?s=tiles_pending', $countTilesPending) . '</span>';
        echo '<span style="padding-top: 11px; padding-bottom: 11px; padding-left: 20px; padding-right: 20px; background: ' . ($GLOBALS['highlight'] == 'tiles_completed' ? '#202326' : '#101316') . ';"><b>Tiles Completed:</b> ' . makeLink('?s=tiles_completed', $countTiles) . '</span>';
        echo '<span style="padding-top: 11px; padding-bottom: 11px; padding-left: 20px; padding-right: 20px; background: ' . ($GLOBALS['highlight'] == 'upload_image' ? '#202326' : '#101316') . ';"><b>Graphix:</b> ' . makeLink('?s=upload_image', $countGalleryImages) . '</span>';
        echo '<span style="padding-top: 11px; padding-bottom: 11px; padding-left: 20px; padding-right: 20px; background: ' . ($GLOBALS['highlight'] == 'quilts_moderate' ? '#202326' : '#101316') . ';"><b>Quilts:</b> ' . makeLink('?s=quilts_moderate', $countQuiltsPermissions) . '</span>';
        echo '<span style="padding-top: 11px; padding-bottom: 11px; padding-left: 20px; padding-right: 20px; background: ' . ($GLOBALS['highlight'] == 'friends' ? '#202326' : '#101316') . ';"><b>Friends:</b> ' . makeLink('?s=friends', $countFriends, 'friends') . '</span>';
    }
    else
    {
        echo '<form method="post" action="?' . server('QUERY_STRING') . '" style="margin: 0px;">' . "\r\n";
        echo '<label for="login_email"><b>Email:</b></label> <input type="text" name="login_email" id="login_email" aria-required="true" class="input_text" style="width: 200px;">' . "\r\n";
        echo '<label for="login_password"><b>Password:</b></label> <input type="password" name="login_password" id="login_password" aria-required="true" class="input_text" style="width: 200px;">' . "\r\n";
        echo '<input type="submit" value="Login" class="input_submit">' . "\r\n";
        echo '</form>' . "\r\n";
    }
?>
</td>
</tr></table>
<div>
<div class="navigation">
<table role="presentation" style="width: 100%; border-spacing: 0;"><tr>
<td class="logo" style="background: #202326; padding: 10px;">
    <a href="?s=finished"><img src="images/quiltsco_logo_gloss.png" style="width: 326px; height: 127px;" alt="Quilts Community Logo"></a>
</td>
<td style="background: #202326; text-align: right; padding-left: 20px; padding-right: 20px;">
    <nav role="navigation" aria-label="Main navigation">
    <ul class="main_navigation">
        <li class="<?php echo $GLOBALS['highlight'] == 'quilts' ? 'active' : '' ?>"><?php echo makeLink('?s=quilts', 'Pending Quilts') ?></li>
        <li class="<?php echo $GLOBALS['highlight'] == 'finished' ? 'active' : '' ?>"><?php echo makeLink('?s=finished', 'Finished Quilts') ?></li>
        <li class="<?php echo $GLOBALS['highlight'] == 'graphix' ? 'active' : '' ?>"><?php echo makeLink('?s=graphix', 'Graphix') ?></li>
        <li class="<?php echo $GLOBALS['highlight'] == 'members' ? 'active' : '' ?>"><?php echo makeLink('?s=members', 'Members') ?></li>
        <li class="<?php echo $GLOBALS['highlight'] == 'forum' ? 'active' : '' ?>"><?php echo makeLink('?s=community&i=1', 'Forum') ?></li>
        <?php
            if ($GLOBALS['auth']['id'])
            {
                echo '<li class="' . ($GLOBALS['highlight'] == 'userinfo' ? 'active' : '') . '">' . makeLink('?s=userinfo', 'Userinfo') . '</li>';
            }
        ?>
    </ul>
    </nav>
</td>
</tr></table>
</div>
<div style="background: #282C2F; padding: 3px 0px 0px 0px;"></div>
</header>
<main role="main" id="main-content">
<div class="major_fill">
<div class="minor_fill">
<div class="fixup_fill">
<?php
    if (isset($_notice) && $_notice)
    {
        echo '<div class="header"><div class="notice_error">';
        echo $_notice;
        echo '</div></div>';
    }
    if (isset($errors) && $errors)
    {
        echo '<div class="header"><div class="notice_error">';
        echo $errors;
        echo '</div></div>';
    }
