<?php
class ExpressPage extends SiteTree {

	static $icon = 'themes/ssexpress/images/icons/sitetree_images/page.png';

	static $db = array(
		'PublicHistory' => 'Boolean',
		'AccessKey' => 'Varchar(1)'
	);

	static $defaults = array(
		'PublicHistory' => true
	);

	public function MenuChildren() {
		return $this->Children()->filter('ShowInMenus', true);
	}

	/**
	 * Compile a list of changes to the current page, excluding non-published and explicitly secured versions.
	 *
	 * @param int $ID Identifier of the record, leave null for this record.
	 * @param int $highestVersion Top version number to consider.
	 * @param boolean $fullHistory Whether to get the full change history or just the previous version.
	 *
	 * @returns ArrayList List of cleaned records.
	 */
	function getDiffedChanges($ID = null, $highestVersion = null, $fullHistory = true) {
		if (!$ID) $record = $this;
		else $record = SiteTree::get()->filter(array('ID'=>(int)$ID))->First();

		if (!$record) return null;

		// This can leak secured content if it was protected via inherited setting.
		// For now the users will need to be aware about this shortcoming.
		$offset = $highestVersion ? "AND \"SiteTree_versions\".\"Version\"<='".(int)$highestVersion."'" : '';
		$limit = $fullHistory ? null : 2;
		$versions = $record->allVersions("\"WasPublished\"='1' AND \"CanViewType\" IN ('Anyone', 'Inherit') $offset", "\"LastEdited\" DESC", $limit);

		// Process the list to add the comparisons.
		$changeList = new ArrayList();
		$previous = null;
		$count = 0;
		foreach ($versions as $version) {
			$changed = false;

			if (isset($previous)) {
				// We have something to compare with.
				$diff = $record->compareVersions($version->Version, $previous->Version);

				// Produce the diff fields for use in the template.
				if ($version->Title != $previous->Title) {
					$version->DiffTitle = new HTMLText();
					$version->DiffTitle->setValue('<div><em>Title has changed:</em> '.$diff->Title.'</div>');
					$changed = true;
				}
				if ($version->Content != $previous->Content) {
					$version->DiffContent = new HTMLText();
					$version->DiffContent->setValue('<div>'.$diff->obj('Content')->forTemplate().'</div>');
					$changed = true;
				}
			}

			// Omit the versions that haven't been visibly changed (only takes the above fields into consideration).
			if ($changed) {
				$changeList->push($version);
				$count++;
			}

			// Store the last version for comparison.
			$previous = $version;
		}

		if ($fullHistory && $previous) {
			$first = clone($previous);
			$first->DiffContent = new HTMLText();
			$first->DiffContent->setValue('<div>'.$first->obj('Content')->forTemplate().'</div>');
			$changeList->push($first);
		}

		return $changeList;
	}

	public function getSettingsFields() {
		$fields = parent::getSettingsFields();

		// Add public history field.
		$fields->addFieldToTab('Root.Settings', $publicHistory = new FieldGroup(
			new CheckboxField('PublicHistory', $this->fieldLabel(_t(
				'RSSHistory.LABEL',
				'Publish public RSS feed containing every published version of this page.'))
		)));
		$publicHistory->setTitle($this->fieldLabel('Public history'));

		// Access key field.
		$fields->addFieldToTab('Root.Settings', new CompositeField(
			$label = new LabelField (
				$name = "extraLabel",
				$content = '<p><em>' . _t(
					'AccessKeys.LABEL',
					'<strong>Note:</strong> Access Keys are optional, but must be a single unique character. Check your current access keys to avoid conflict'
				) . '</em></p>'
			),
			new CompositeField(
				new TextField('AccessKey', $title = 'Access Key', $value = '', $maxLength = 1)
			)
		));

		return $fields;
	}

	public function getSiteRSSLink() {
		$homepage = ExpressHomePage::get_one('ExpressHomePage');
		if ($homepage) {
			return $homepage->Link('allchanges');
		}

		return RootURLController::get_homepage_link() . '/allchanges';
	}

	public function getDefaultRSSLink() {
		if ($this->PublicHistory) return $this->Link('changes');
	}
}

class ExpressPage_Controller extends ContentController {
	public static $allowed_actions = array (
	);

	public function init() {
		parent::init();

		// Requirements
		$themeDir = SSViewer::get_theme_folder();
		Requirements::clear();
		Requirements::combine_files(
			'ssexpress.js',
			array(
				"$themeDir/js/lib/jquery.js",
				"$themeDir/js/lib/jquery-ui-1.8.21.custom.js",
				'themes/module_bootstrap/js/bootstrap-transition.js',
//				'themes/module_bootstrap/js/bootstrap-alert.js',
//				'themes/module_bootstrap/js/bootstrap-modal.js',
				'themes/module_bootstrap/js/bootstrap-scrollspy.js',
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
				"$themeDir/css/typography.css"
			)
		);
		Requirements::css("$themeDir/css/print.css", 'print');

		Requirements::set_combined_files_folder("$themeDir/_compiled");

		// RSS feed for per-page changes.
		if ($this->PublicHistory) {
			RSSFeed::linkToFeed($this->Link() . 'changes', 'Updates to ' . $this->Title . ' page');
		}
		// RSS feed to all-site changes.
		RSSFeed::linkToFeed($this->getSiteRSSLink(), 'Updates to ' . SiteConfig::current_site_config()->Title);
	}

	/**
	 * Get page-specific changes in a RSS feed.
	 */
	function changes() {
		if(!$this->PublicHistory) throw new SS_HTTPResponse_Exception('Page history not viewable', 404);

		// Generate the output.
		$rss = new RSSFeed($this->getDiffedChanges(), $this->request->getURL(), 'Updates to ' . $this->Title . ' page', '', "Title", "", null);
		$rss->setTemplate('Page_changes_rss');
		$rss->outputToBrowser();
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

	function getFooter() {
		return FooterHolder::get_one('FooterHolder');
	}
}
