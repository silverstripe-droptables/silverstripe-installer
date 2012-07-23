<?php
class ExpressHomePage extends Page {

	static $icon = "themes/ssexpress/images/icons/sitetree_images/home.png";
	public $pageIcon =  "images/icons/sitetree_images/home.png";

}
class ExpressHomePage_Controller extends Page_Controller {

	/**
	 * @param int $amount The amount of items to provide.
	 */
	public function getNewsItems($amount = 2) {
		$newsHolder = NewsHolder::get_one('NewsHolder');
		if ($newsHolder) {
			$controller = new NewsHolder_Controller($newsHolder);
			return $controller->getNewsItems($amount);
		}
	}

}
