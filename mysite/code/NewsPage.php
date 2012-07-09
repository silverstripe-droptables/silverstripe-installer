<?php

class NewsPage extends Page {
	static $default_parent = 'NewsHolderPage';
	static $can_be_root = false;

	static $db = array(
		'Date' => 'Date',
		'Abstract' => 'Text',
		'Author' => 'Varchar(255)'
	);

	static $has_one = array(
		'Category' => 'NewsCategory'
	);

	/**
	 * Add the default for the Date being the current day.
	 */
	public function populateDefaults() {
		parent::populateDefaults();

		if(!isset($this->Date) || $this->Date === null) {
			$this->Date = strftime('%d/%m/%Y');
		}
		
		$this->extend('populateDefaults');
	}

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->addFieldToTab('Root.Main', $dateField = new DateField('Date'), 'Content');
		$dateField->setConfig('showcalendar', true);

		$categories = NewsCategory::get()->sort('Title DESC');
		if ($categories && $categories->exists()) {
			$fields->addFieldToTab('Root.Main', new DropdownField('CategoryID', 'Category', $categories->map()), 'Content');
		}

		$fields->addFieldToTab('Root.Main', new TextareaField('Abstract'), 'Content');

		return $fields;
	}
}

class NewsPage_Controller extends Page_Controller {
	
}