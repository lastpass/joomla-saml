<?php

$lpsaml_base = JUri::base();

// When we come from module.php, this function will be relative
// to module.php instead of joomla's index.php.  So trim off
// the rest of the URL as needed.
$lpsaml_base = preg_replace(
    ',plugins/authentication/lpsaml/simplesamlphp/www/$,', '',
    $lpsaml_base);

$config = array(
    'default-sp' => array (
        'saml:SP',
        'entityID' => "LastPass:Joomla:" . $lpsaml_base,
        'idp' => 'https://lastpass.com/saml/idp',
    ),
);
