<?php
/**
 * Adds new global settings.
 */

class CustomSiteConfig extends DataExtension {
	function extraStatics($class = null, $extension = null) {
		return array(
			'db' => array(
				'GACode' => 'Varchar(16)'
			)
		);
	}

	function updateCMSFields(FieldList $fields) {
		$fields->addFieldToTab('Root.Main', $gaCode = new TextField('GACode', 'Google Analytics account'));
		$gaCode->setRightTitle('Account number to be used all across the site (UA-XXXXX-X)');
	}
}
