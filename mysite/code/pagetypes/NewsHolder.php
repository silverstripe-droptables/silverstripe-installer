<?php

class NewsHolder extends Page {
	static $allowed_children = array('NewsPage');
	static $default_child = 'NewsPage';
}

class NewsHolder_Controller extends Page_Controller {

	public function NewsItems($pageSize = 10) {
		$items = $this->Children()->filter('ClassName', 'NewsPage')->sort('Date', 'DESC');
		$list = new PaginatedList($items, $this->request);
		$list->setPageLength($pageSize);
		return $list;
	}
	
}