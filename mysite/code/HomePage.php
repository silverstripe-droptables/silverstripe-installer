<?php
class HomePage extends Page {

}
class HomePage_Controller extends Page_Controller {

	public function NewsItems() {
		$newsHolder = NewsHolder::get_one('NewsHolder');
		if ($newsHolder) {
			$controller = new NewsHolder_Controller($newsHolder);
			return $controller->NewsItems(5);
		}
	}

}