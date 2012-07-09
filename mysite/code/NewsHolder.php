<?php

class NewsHolder extends Page {
	static $allowed_children = array('NewsPage');
	static $default_child = 'NewsPage';
}

class NewsHolder_Controller extends Page_Controller {
	
}