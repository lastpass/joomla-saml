<?php

/**
 * This class pulls metadata from the Joomla configuration of
 * the LastPass SAML plugin.  This allows configuring the
 * IdP inside Joomla instead of through saml20-idp-remote.php.
 */
class SimpleSAML_Metadata_MetaDataStorageHandlerJoomla extends SimpleSAML_Metadata_MetaDataStorageSource {

	/**
	 * Initialize the handler
	 *
	 * @param array $config  The configuration for this instance
	 */
	protected function __construct($config) {
		assert('is_array($config)');
	}


	/**
	 * This function returns an associative array with metadata for all entities in the given set. The
	 * key of the array is the entity id.
	 *
	 * @param $set  The set we want to list metadata for.
	 * @return An associative array with all entities in the given set.
	 */
	public function getMetadataSet($set) {

		/* We don't have this metadata set. */
		return array();
	}

	/*
	 * Remove BEGIN/END-CERTIFICATE and whitespace from
	 * cert string.
	 */
	private static function cleanCert($cert) {
		$cert = preg_replace("/-----(BEGIN|END) CERTIFICATE-----/", "", $cert);
		$cert = preg_replace("/\r|\n| /", "", $cert);
		return $cert;
	}

	/**
	 * Overriding this function from the superclass
	 * SimpleSAML_Metadata_MetaDataStorageSource.
	 *
	 * Look inside the LP SAML configuration for the given entity's
	 * metadata.
	 *
	 * It will return NULL if it is unable to locate the metadata.
	 *
	 * @param $index  The entityId or metaindex we are looking up.
	 * @param $set  The set we are looking for metadata in.
	 * @return An associative array with metadata for the given
	 * entity, or NULL if we are unable to locate the entity.
	 */
	public function getMetaData($index, $set) {
		assert('is_string($index)');
		assert('is_string($set)');

		$lpsaml = JPluginHelper::getPlugin('authentication',
						   'lpsaml');
		$params = json_decode($lpsaml->params);
		if ($params->idp_entity_id != $index) {
			SimpleSAML_Logger::info('MetaData - Handler.Joomla: unable to find EntityID/index [' . $index . '] in config.');
			return NULL;
		}

		$data = array (
			'entityid' => $index,
			'name' => array(
				'en' => 'Joomla SAML IdP',
			),
			'description' => 'IdP for Joomla SAML plugin',
			'SingleSignOnService' => $params->idp_login,
			'SingleSignOutService' => $params->idp_logout,
			'certData' => self::cleanCert($params->idp_cert)
		);

		return $data;
	}
}

?>
