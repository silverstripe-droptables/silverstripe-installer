<?php
class ExpressPage extends SiteTree {

	static $icon = 'themes/ssexpress/images/icons/sitetree_images/page.png';

	static $db = array(
		'PublicHistory' => 'Boolean'
	);

	static $defaults = array(
		'PublicHistory' => true
	);

	public function MenuChildren() {
		return $this->Children()->filter('ShowInMenus', true);
	}	

	static $db = array(
		'AccessKey' => 'Varchar(1)'
	);

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
				if ($previous->Title!=$version->Title) {
					$version->DiffTitle = new HTMLText();
					$version->DiffTitle->setValue('<div><em>Title has changed:</em> '.$diff->Title.'</div>');
					$changed = true;
				}
				if ($previous->Content!=$version->Content) {
					$version->DiffContent = new HTMLText();
					$version->DiffContent->setValue('<div>'.$diff->obj('Content')->forTemplate().'</div>');
					$changed = true;
				}

				// Omit the versions that haven't been visibly changed (only takes the above fields into consideration).
				if ($changed) {
					$changeList->push($version);
					$count++;
				}
			}

			// Store the last version for comparison.
			$previous = $version;
		}		

		return $changeList;
	}

	function getSettingsFields() {
		$fields = parent::getSettingsFields();

		$fields->addFieldToTab('Root.Settings', $publicHistory = new FieldGroup(
			new CheckboxField('PublicHistory', $this->fieldLabel('Make all page history viewable (This will make the entire page history visible, even if it used to be secured)'))
		));

		$publicHistory->setTitle($this->fieldLabel('Public history'));

		return $fields;
	}

	public function getSettingsFields() {
        $fields = parent::getSettingsFields();
        

        $accessKey = new CompositeField(
	        $label = new LabelField (
	    			$name = "extraLabel",
	    			$content = '<em><strong>Note:</strong> Access Keys must be unique and no longer than a character in length. Please check your current access keys</em>'
	 		),
	 		new CompositeField(
	        	new TextField('AccessKey', $title='Access Key', $value='', $maxLength=1)
	        )
        );

   

        $fields->addFieldToTab('Root.Settings', $accessKey);


        return $fields;
    }


}

class ExpressPage_Controller extends ContentController {
	public static $allowed_actions = array (
	);

	public function init() {
		parent::init();

		$themeDir = SSViewer::get_theme_folder();
		Requirements::clear();
		Requirements::combine_files(
			'ssexpress.js',
			array(
				"$themeDir/js/lib/jquery.js",
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

		Requirements::set_combined_files_folder("$themeDir/_compiled");
	}


	/**
	 * Get all changes from the site in a RSS feed.
	 */
	function allchanges() {
		// Fetch the latest changes on the entire site.
		$latestChanges = DB::query("SELECT v.* FROM \"SiteTree_versions\" v LEFT JOIN \"ExpressPage\" e ON v.\"RecordID\" = e.\"ID\" WHERE v.\"WasPublished\"='1' AND v.\"CanViewType\" IN ('Anyone', 'Inherit') AND v.\"ShowInSearch\"=1 AND (e.\"PublicHistory\" IS NULL OR e.\"PublicHistory\" = '1') ORDER BY v.\"LastEdited\" DESC LIMIT 20");

		$changeList = new ArrayList();
		foreach ($latestChanges as $record) {
			// Get the diff to the previous version.
			$version = new Versioned_Version($record);
			$changes = $this->getDiffedChanges($version->RecordID, $version->Version, false);
			if ($changes->Count()) $changeList->push($changes->First());
		}

		// Produce output
		$rss = new RSSFeed($changeList, $this->request->getURL(), 'Changes feed', '', "Title", "", null);
		$rss->setTemplate('Page_allchanges_rss');
		$rss->outputToBrowser();
	}

	/**
	 * Get page-specific changes in a RSS feed.
	 */
	function changes() {
		if(!$this->PublicHistory) throw new SS_HTTPResponse_Exception('Page history not viewable', 404);

		// Generate the output.
		$rss = new RSSFeed($this->getDiffedChanges(), $this->request->getURL(), 'Changes feed', '', "Title", "", null);
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
}
