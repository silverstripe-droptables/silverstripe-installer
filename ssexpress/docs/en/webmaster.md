# Webmaster Howto

This howto is intended for staff who will be deploying and maintaining the site, as well as customising the themes and applying minor changes to the codebase.

## Installation

To install SilverStripe Express on your server:

* Download SilverStripe Express tarball
* Unpack it
* Check that you have the required modules (listed in /dependent-modules) and fetch any that you don't have. Only cms, framework and themes/ssexpress are required, the rest offer optional functionality.
* Amend `mysite/_config.php` to configure the database name
* The site is ready to go

You can also fetch SilverStripe Express from source. The base repository is on github, the `ssexpress` branch of `github.com/silverstripe-droptables/silverstripe-installer`. The other repositories and their folders are listed in the file `dependent-modules` in the root - these are automatically fetched when using the Phing build tool with the command `phing update_modules`.

## Working with the templates

We recommend creating a new template for each site you build. The `ssexpress` template supplied with the tarball is a good baseline to start from - you can just copy it to a new folder so you won't overwrite it on upgrade.

### From source

If you want to work from source it's recommended that you edit the SCSS instead of the CSS directly, and use the Compass framework to compile the SCSS. Once you've installed Compass from [http://compass-style.org/install/](http://compass-style.org/install/) you can start the automatic compilation process by running `compass watch -e production` in the `themes/ssexpress` directory. This will require that you've got module_bootstrap present in your themes directory (you may not if you've installed from source and omitted it) as the SCSS files in the ssexpress theme depend on it.

## Working with the code

SilverStripe Express comes with a set of page types and preconfigured modules. 

You can disable modules by either removing them, or renaming the `_config.php` file within their root directory. 

SilverStripe Express specific page types are all located in `ssexpress` directory, and some stub page types have been created for you in `mysite`.

Use `mysite/_config.php` to fine-tune the site configuration. More information is available in the [configuration reference](http://doc.silverstripe.org/framework/en/topics/configuration).

## Upgrading an existing site

SilverStripe Express can be used to enhance an existing site:

* Add the ssexpress folder to the root of the site like any other module
* Update the mysite/_config.php with the options from any modules that you'd like to include (at the very least, the replacing of SiteConfig with CustomSiteConfig)
* Alter all classes that inherit from SiteTree to inherit from ExpressPage instead
* run /dev/build?flush=all

This will provide the new page types (NewsPage, NewsHolder, AccessibilityPage, SitemapPage) as well as giving all the pages RSS feeds and the option to have an access key. You may face integration issues with an existing site search, this could remedied by overriding the results() function in the lowest-level Page class.

SilverStripe Express has no dependencies on the other modules that ship with it (documentconverter, iframe, sitetreeimporter, translatable, userforms and versionedfiles) so these maybe omitted if the functionality isn't necessary.

## Future upgrades

It is not recommended to modify directly any module files. The best way to work with the site is to put modifications in your own theme (`themes/<yourname>`) or in `mysite` only.

If you wish to amend the orignal page types, use inheritance:

```php
class MyNewsPage extends NewsPage {
	static $hide_ancestor = 'NewsPage';
	// Your custom code here
}

class MyNewsPage_Controller extends NewsPage_Controller {
	// Your custom code here
}
```

You can also use `DataExtensions` and `Extensions`, described in further detail in the [documentation](http://doc.silverstripe.org/framework/en/reference/dataextension).

Because of additional code merging, if modifications are made directly to module files, upgrades will require more effort and testing.

## Building a new theme

tbd
