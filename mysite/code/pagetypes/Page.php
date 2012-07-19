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

		$themeDir = SSViewer::get_theme_folder();
		Requirements::clear();
		Requirements::combine_files(
			'ssexpress.js',
			array(
				"$themeDir/js/lib/modernizr.js",
				"$themeDir/js/lib/jquery.js",
				'themes/module_bootstrap/js/bootstrap-transition.js',
//				'themes/module_bootstrap/js/bootstrap-alert.js',
//				'themes/module_bootstrap/js/bootstrap-modal.js',
//				'themes/module_bootstrap/js/bootstrap-scrollspy.js',
//				'themes/module_bootstrap/js/bootstrap-tab.js',
//				'themes/module_bootstrap/js/bootstrap-tooltip.js',
//				'themes/module_bootstrap/js/bootstrap-popover.js',
//				'themes/module_bootstrap/js/bootstrap-button.js',
				'themes/module_bootstrap/js/bootstrap-collapse.js',
				'themes/module_bootstrap/js/bootstrap-carousel.js',
//				'themes/module_bootstrap/js/bootstrap-typeahead.js',
				"$themeDir/js/general.js",
				"$themeDir/js/express.js",
				"$themeDir/js/forms.js"
			)
		);
		Requirements::combine_files(
			'ssexpress.css',
			array(
				"$themeDir/css/layout.css",
				"$themeDir/css/typography.css",
				"$themeDir/css/forms.css"
			)
		);
	}

	/**
	 * Overrides the ContentControllerSearchExtension and adds snippets to results.
	 */
	function results($data, $form, $request) {
		$results = $form->getResults();
		$query = $form->getSearchQuery();

		// Add context summaries based on the queries.
		foreach ($results as $result) {
			$contextualTitle = new Text();
			$contextualTitle->setValue($result->MenuTitle);
			$result->ContextualTitle = $contextualTitle->ContextSummary(300, $query);

			$result->ContextualContent = $result->obj('Content')->ContextSummary(300, $query);
		}

		// Render the result.
		$data = array(
			'Results' => $results,
			'Query' => $query,
			'Title' => _t('SearchForm.SearchResults', 'Search Results')
		);
		return $this->owner->customise($data)->renderWith(array('Page_results', 'Page'));
	}
}
