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
		Requirements::javascript("$themeDir/js/lib/modernizr.js");
		Requirements::combine_files(
			'ssexpress.js',
			array(
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
			$contextualTitle->setValue($result->MenuTitle ? $result->MenuTitle : $result->Title);
			$result->ContextualTitle = $contextualTitle->ContextSummary(300, $query);

			if (!$result->Content && $result->ClassName=='File') {
				// Fake some content for the files.
				$result->ContextualContent = "A file named \"$result->Name\" ($result->Size).";
			}
			else {
				$result->ContextualContent = $result->obj('Content')->ContextSummary(300, $query);
			}
		}

		$rssLink = HTTP::setGetVar('rss', '1');

		// Render the result.
		$data = array(
			'Results' => $results,
			'Query' => $query,
			'Title' => _t('SearchForm.SearchResults', 'Search Results'),
			'RSSLink' => $rssLink
		);

		// Choose the delivery method - rss or html.
		if(!$this->request->getVar('rss')) {
			// Add RSS feed to normal search.
			RSSFeed::linkToFeed($rssLink, "Search results for query \"$query\".");

			return $this->owner->customise($data)->renderWith(array('Page_results', 'Page'));
		}
		else {
			// De-paginate and reorder. Sort-by-relevancy doesn't make sense in RSS context.
			$fullList = $results->getList()->sort('LastEdited', 'DESC');

			// Get some descriptive strings
			$siteName = SiteConfig::current_site_config()->Title;
			$siteTagline = SiteConfig::current_site_config()->Tagline;
			if ($siteName) {
				$title = "$siteName search results for query \"$query\".";
			}
			else {
				$title = "Search results for query \"$query\".";
			}

			// Generate the feed content.
			$rss = new RSSFeed($fullList, $this->request->getURL(), $title, $siteTagline, "Title", "ContextualContent", null);
			$rss->setTemplate('Page_results_rss');
			$rss->outputToBrowser();
		}
	}
}
