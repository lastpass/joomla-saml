<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Authentication.lpsaml
 *
 * @copyright   Copyright (C) 2014 LastPass
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * LastPass SAML Authentication Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Authentication.lpsaml
 * @since       1.5
 */
class PlgAuthenticationLPSaml extends JPlugin
{
	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param   array   $credentials  Array holding the user credentials
	 * @param   array   $options      Array of extra options
	 * @param   object  &$response    Authentication response object
	 *
	 * @return  boolean
	 *
	 * @since   1.5
	 */
	public function onUserAuthenticate($credentials, $options, &$response)
	{
		require_once JPATH_SITE . '/plugins/authentication/lpsaml/simplesamlphp/lib/_autoload.php';

		$domain = JFactory::getConfig()->get('cookie_domain', '');
		setcookie('lpsaml_login_context', $options['action'], 0,
			  '/', $domain);

		$as = new SimpleSAML_Auth_Simple('default-sp');
		$as->requireAuth();

		$attributes = $as->getAttributes();
		if (!isset($attributes['uid'])) {
			$response->status = JAuthentication::STATUS_FAILURE;
			return False;
		}
		$response->type = 'SAML';
		$response->status = JAuthentication::STATUS_SUCCESS;
		$response->username = $attributes['uid'][0];
		$response->email = $response->username;
		if (isset($attributes['Name']))
			$response->fullname = $attributes['Name'][0];
		$response->password_clear = self::generatePassword();

		return True;
	}

	function generatePassword()
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
		$pwlen = 20;
		$pw = '';
		$bytes = openssl_random_pseudo_bytes($pwlen);
		for ($i = 0; $i < $pwlen; $i++) {
			$pw .= $chars[ord($bytes[$i]) % strlen($chars)];
		}
		return $pw;
	}
}
