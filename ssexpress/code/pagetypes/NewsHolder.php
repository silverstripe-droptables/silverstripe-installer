<?php

class NewsHolder extends Page {
	static $allowed_children = array('NewsPage');
	static $default_child = 'NewsPage';
	static $icon = "themes/ssexpress/images/icons/sitetree_images/news_listing.png";
	public $pageIcon =  "images/icons/sitetree_images/news_listing.png";

	public function MenuChildren() {
		return parent::MenuChildren()->exclude('ClassName', 'NewsPage');
	}

	public function getCategories() {
		return NewsCategory::get()->sort('Title', 'DESC');
	}
}

class NewsHolder_Controller extends Page_Controller {

	public function getNewsItems($pageSize = 10) {
		$items = $this->Children()->filter('ClassName', 'NewsPage')->sort('Date', 'DESC');
		$category = $this->getCategory();
		if ($category) $items->filter('CategoryID', $category->ID);
		$list = new PaginatedList($items, $this->request);
		$list->setPageLength($pageSize);
		return $list;
	}

	public function getCategory() {
		$categoryID = $this->request->getVar('category');
		if (!is_null($categoryID)) {
			return NewsCategory::get_by_id('NewsCategory', $categoryID);
		}
	}
}