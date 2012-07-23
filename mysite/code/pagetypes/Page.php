<?php
/**
 * This base class is provided here so you can add modifications on top
 * of the SilverStripe Express code. It is not recommended to modify the 
 * code in ssexpress directory directly so it's easy to upgrade in the
 * future.
 */

class Page extends ExpressPage {
	static $hide_ancestor = 'ExpressPage';
}

class Page_Controller extends ExpressPage_Controller {

}
