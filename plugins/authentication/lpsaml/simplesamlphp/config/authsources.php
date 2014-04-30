<?php

$lpsaml_base = JUri::base();

// When we come from module.php, this function will be relative
// to module.php instead of joomla's index.php.  So trim off
// the rest of the URL as needed.
$lpsaml_base = preg_replace(
    ',plugins/authentication/lpsaml/simplesamlphp/www/$,', '',
    $lpsaml_base);

// Likewise, if we come from administrator (instead of site)
// the base URI will reflect this.  We want to use just one
// entity ID for either site or admin.  A separate cookie tells
// SSP which one it's for.
$lpsaml_base = preg_replace(',administrator/$,', '', $lpsaml_base);

$config = array(
    'default-sp' => array (
        'saml:SP',
        'entityID' => "LastPass:Joomla:" . $lpsaml_base,
        'idp' => 'https://lastpass.com/saml/idp',
    ),
);
