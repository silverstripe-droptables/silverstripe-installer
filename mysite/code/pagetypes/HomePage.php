<?php
/**
 * This base class is provided here so you can add modifications on top
 * of the SilverStripe Express code. It is not recommended to modify the 
 * code in ssexpress directory directly so it's easy to upgrade in the
 * future.
 */

class HomePage extends ExpressHomePage {
	static $hide_ancestor = 'ExpressHomePage';
}

class HomePage_Controller extends ExpressHomePage_Controller {

}

