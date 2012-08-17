<?php
/**
 * Adds new global settings.
 */

class CustomSiteConfig extends DataExtension {
	function extraStatics($class = null, $extension = null) {
		return array(
			'db' => array(
				'GACode' => 'Varchar(16)',
				'FacebookURL' => 'Varchar(256)', // multitude of ways to link to Facebook accounts, best to leave it open.
				'TwitterUsername' => 'Varchar(16)', // max length of Twitter username 15
				'AddThisProfileID' => 'Varchar(32)'
			)
		);
	}

	function updateCMSFields(FieldList $fields) {
		$fields->addFieldToTab('Root.Main', $gaCode = new TextField('GACode', 'Google Analytics account'));
		$gaCode->setRightTitle('Account number to be used all across the site (in the format <strong>UA-XXXXX-X</strong>)');

		$fields->addFieldToTab('Root.Main', $gaCode = new TextField('FacebookURL', 'Facebook UID or username'));
		$gaCode->setRightTitle('Facebook link (everything after the "http://facebook.com/", eg http://facebook.com/<strong>username</strong> or http://facebook.com/<strong>pages/108510539573</strong>)');

		$fields->addFieldToTab('Root.Main', $gaCode = new TextField('TwitterUsername', 'Twitter username'));
		$gaCode->setRightTitle('Twitter username (eg, http://twitter.com/<strong>username</strong>)');

		$fields->addFieldToTab('Root.Main', $addThisID = new TextField('AddThisProfileID', 'AddThis Profile ID'));
		$addThisID->setRightTitle('Profile ID to be used all across the site (in the format <strong>ra-XXXXXXXXXXXXXXXX</strong>)');
	}
}
