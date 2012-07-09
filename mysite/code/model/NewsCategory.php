<?php

class NewsCategory extends DataObject {
	static $has_many = array(
		'NewsItems' => 'NewsPage'
	);

	static $db = array(
		'Title' => 'Varchar(255)'
	);
}