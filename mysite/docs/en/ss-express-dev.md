# SS Express development

## Cloning the repository for development

Prerequisites: `phing`, `Pear::VersionControl_Git`

We currently work on `ssexpress` branch on all modules and the root repo, so we can switch the versions all across the board easily.

To set up the developer's version of the repo:

* Clone the repository: `git@github.com:silverstripe-droptables/silverstripe-installer.git`
* Make sure you are on correct branch: `git checkout ssexpress`
* Load all modules in writeable mode: `phing update_modules` (they default to ssexpress branch as well)

## New release

We currently release the package as a tar.gz archive, while tagging all the related modules.

* Tag the release: `phing tag -Dtagname ssexpress-0.1.0 -DincludeBaseDir yes`, say yes to push the tags to origin
* Create the archive: `phing archive -Dversion ssexpress-0.1.0 -Darchivedest releases -Darchivename ssexpress-0.1.0 -Darchivetype tar.gz`

## Re-releasing

* Checkout the release tag: `phing checkout -Dtagname ssexpress-0.1.0 -DincludeBaseDir yes`
* Create the archive `phing archive -Dversion ssexpress-0.1.0 -Darchivedest releases -Darchivename ssexpress-0.1.0 -Darchivetype tar.gz`
* Checkout the latest version again: `phing checkout -Dtagname ssexpress -DincludeBaseDir yes`
