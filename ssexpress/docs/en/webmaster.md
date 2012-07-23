# Webmaster Howto

This howto is intended for staff who will be deploying and maintaining the site, as well as customising the themes and applying minor changes to the codebase.

## Installation

To install SS Express on your server:

* Dowload SS Express tarball
* Unpack it
* Amend `mysite/_config.php` to configure the database name
* The site is ready to go

## General guidelines to working with SS Express

SilverStripe Express comes with a set of page types and preconfigured modules. 

You can disable modules by either removing them, or renaming the `_config.php` file within their root directory. 

SilverStripe Express specific page types are all located in `ssexpress` directory, and some stub page types have been created for you in `mysite`.

Use `mysite/_config.php` to fine-tune the site configuration. More information is available in the [configuration reference](http://doc.silverstripe.org/framework/en/topics/configuration).

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
