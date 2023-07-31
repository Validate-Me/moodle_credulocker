<?php
/*  DOCUMENTATION
    .............

    Create a 'lang' and inside 'en' folder (lang/en), where 'en' is the English language file for your block. If you are not
    an English speaker, you can replace 'en' with your appropriate language code. All language files for blocks go under the
    /lang subfolder of the block's installation folder.

    Strings are defined via the associative array $string provided by the string file. The array key is the string identifier,
    the value is the string text in the given language. Moodle supports over 100 languages (en (english), fr(french) etc.,).
    en (English) is the default language.

    It is mandatory that any manual text must be written in language strings for Moodle to identify the language defined in
    lang folder.

*/

$string['pluginname'] = 'Credulocker - wallet for credentials'; // Name of your plugin.
$string['slack'] = 'Claim Your Certificate'; // Block header name.
$string['claim_certificate'] = 'Get certificate';

// Strings:access.
$string['slack:addinstance'] = 'Add a slack block';
$string['slack:myaddinstance'] = 'Add a slack block to My Moodle page';

// Strings:block_custom.
$string['slcontent'] = "Claim Certificate";

// Strings:settings.
$string['settings_heading'] = 'General settings';
$string['settings_content'] = 'The general settings for your block Slack';
$string['label'] = 'Your Label';
$string['label_desc'] = 'Your Description';
$string['claim_certificate'] = 'Claim  Certificate';
