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

	/**
	 * An array of actions that can be accessed via a request. Each array element should be an action name, and the
	 * permissions or conditions required to allow the user to access it.
	 *
	 * <code>
	 * array (
	 *     'action', // anyone can access this action
	 *     'action' => true, // same as above
	 *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
	 *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
	 * );
	 * </code>
	 *
	 * @var array
	 */
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

		// Note: you should use SS template require tags inside your templates 
		// instead of putting Requirements calls here.  However these are 
		// included so that our older themes still work
		Requirements::themedCSS('layout'); 
		Requirements::themedCSS('typography'); 
		Requirements::themedCSS('forms'); 
	}



}