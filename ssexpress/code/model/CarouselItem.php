<?php

class CarouselItem extends DataObject {
	static $db = array(
		'Title' => 'Varchar(255)',
		'Caption' => 'Text'
	);

	static $has_one = array(
		'Parent' => 'ExpressHomePage',
		'Image' => 'Image',
		'Link' => 'SiteTree'
	);

	function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->removeByName('ParentID');

		return $fields;
	}
}