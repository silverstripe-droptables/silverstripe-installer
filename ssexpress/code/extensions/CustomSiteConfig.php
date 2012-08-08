<?php
/**
 * Adds new global settings.
 */

class CustomSiteConfig extends DataExtension {
	function extraStatics($class = null, $extension = null) {
		return array(
			'db' => array(
				'GACode' => 'Varchar(16)',
				'AddThisProfileID' => 'Varchar(32)'
			)
		);
	}

	function updateCMSFields(FieldList $fields) {
		$fields->addFieldToTab('Root.Main', $gaCode = new TextField('GACode', 'Google Analytics account'));
		$gaCode->setRightTitle('Account number to be used all across the site (UA-XXXXX-X)');

		$fields->addFieldToTab('Root.Main', $addThisID = new TextField('AddThisProfileID', 'AddThis Profile ID'));
		$addThisID->setRightTitle('Profile ID to be used all across the site (ra-XXXXXXXXXXXXXXXX)');
	}
}
