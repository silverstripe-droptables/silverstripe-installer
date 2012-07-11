<?php
class HomePage extends Page {

}
class HomePage_Controller extends Page_Controller {

	public function getNewsItems() {
		$newsHolder = NewsHolder::get_one('NewsHolder');
		if ($newsHolder) {
			$controller = new NewsHolder_Controller($newsHolder);
			return $controller->getNewsItems(5);
		}
	}

}