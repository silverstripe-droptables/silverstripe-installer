<?php
class ExpressHomePage extends Page {

	static $icon = "themes/ssexpress/images/icons/sitetree_images/home.png";
	public $pageIcon =  "images/icons/sitetree_images/home.png";

	static $db = array(
		'FeatureOneTitle' => 'Varchar(255)',
		'FeatureOneCategory' => "Enum('comments, group, news', 'comments')",
		'FeatureOneContent' => 'HTMLText',
		'FeatureOneButtonText' => 'Varchar(255)',
		'FeatureTwoTitle' => 'Varchar(255)',
		'FeatureTwoCategory' => "Enum('comments, group, news', 'group')",
		'FeatureTwoContent' => 'HTMLText',
		'FeatureTwoButtonText' => 'Varchar(255)'
	);

	static $has_one = array(
		'LearnMorePage' => 'SiteTree',
		'FeatureOneLink' => 'SiteTree',
		'FeatureTwoLink' => 'SiteTree'
	);

	static $has_many = array(
		'CarouselItems' => 'CarouselItem',
		'Quicklinks' => 'Quicklink'
	);

	function getCMSFields() {
		$fields = parent::getCMSFields();

		// Main Content tab
		$fields->addFieldToTab('Root.Main', new TreeDropdownField('LearnMorePageID', 'Page to link the "Learn More" button to:', 'SiteTree'), 'Metadata');
		// Carousel tab
		$gridField = new GridField(
			'CarouselItems',
			'Carousel',
			$this->CarouselItems()->sort('Archived'),
			GridFieldConfig_RelationEditor::create());
		$gridField->setModelClass('CarouselItem');
		$fields->addFieldToTab('Root.Carousel', $gridField);

		$gridField = new GridField(
			'Quicklinks',
			'Quicklinks',
			$this->Quicklinks(),
			GridFieldConfig_RelationEditor::create());
		$gridField->setModelClass('Quicklink');
		$fields->addFieldToTab('Root.Quicklinks', $gridField);

		$fields->removeByName('Translations');
		$fields->removeByName('Import');

		$fields->addFieldToTab('Root.Features',ToggleCompositeField::create('FeatureOne', _t('SiteTree.FeatureOne', 'Feature One'),
			array(
				new TextField('FeatureOneTitle', 'Title'),
				new TreeDropdownField('FeatureOneLinkID', 'Page to link to', 'SiteTree'),
				new HTMLEditorField('FeatureOneContent', 'Content')				
				)
			)->setHeadingLevel(3)
		);

		$fields->addFieldToTab('Root.Features',ToggleCompositeField::create('FeatureTwo', _t('SiteTree.FeatureTwo', 'Feature Two'),
			array(
				new TextField('FeatureTwoTitle', 'Title'),
				new TreeDropdownField('FeatureTwoLinkID', 'Page to link to', 'SiteTree'),
				new HTMLEditorField('FeatureTwoContent', 'Content')
				)
			)->setHeadingLevel(3)
		);

		return $fields;
	}

	function getVisibleCarouselItems() {
		return $this->CarouselItems()->filter('Archived', false);
	}
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
			if ($changes && $changes->Count()) $changeList->push($changes->First());
		}

		// Produce output
		$rss = new RSSFeed($changeList, $this->request->getURL(), 'Updates to ' . SiteConfig::current_site_config()->Title, '', "Title", "", null);
		$rss->setTemplate('Page_allchanges_rss');
		return $rss->outputToBrowser();
	}

}
