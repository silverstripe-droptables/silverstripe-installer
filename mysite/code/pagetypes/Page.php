<?php
class Page extends SiteTree {

	public static $db = array(
	);

	public static $has_one = array(

	);

	public function MenuChildren() {
		return $this->Children()->filter('ShowInMenus', true);
	}

	static $icon = "themes/ssexpress/images/icons/sitetree_images/page.png";


}
class Page_Controller extends ContentController {
	public static $allowed_actions = array (
	);

	public function init() {
		parent::init();

		Requirements::combine_files(
			'ssexpress.js',
			array(
				'themes/ssexpress/js/general.js',
				'themes/ssexpress/js/express.js'
			)
		);
	}
}
