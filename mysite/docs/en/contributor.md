# Contributor Howto

This document is intended for developers who wish to contribute code back to the source of SS Express.

## Cloning the repository for development

Prerequisites: `phing`, `Pear::VersionControl_Git` (see below)

We work on `ssexpress` branch on all modules and the root repo, so we can switch the versions all across the board easily.

### With write access to silverstripe-droptables

* Clone the repository: `git clone git@github.com:silverstripe-droptables/silverstripe-installer.git`
* Make sure you are on correct branch: `git checkout ssexpress`
* Load all modules in writeable mode: `phing update_modules` (they default to ssexpress branch as well)

### Without write access to silverstrpe-droptables

* Fork the root repo and all module repos (check dependent-modules.default for full list)
* Clone your fork, checkout ssexpress branch
* Amend the dependent-modules to point to your own forks, commit the change
* Load all modules in writeable mode: `phing update_modules` (make sure they are pointing to ssexpress branches)

From here you can submit pull requests from your forks as normal.

## New release

We release the package as a tar.gz archive, while tagging all the related modules.

* Tag the release: `phing tag -Dtagname ssexpress-0.1.0 -DincludeBaseDir yes`, say yes to push the tags to origin
* Create the archive: `phing archive -Dversion ssexpress-0.1.0 -Darchivedest releases -Darchivename ssexpress-0.1.0 -Darchivetype tar.gz`

## Re-releasing

To get a tar.gz file for an old release, do:

* Checkout the release tag: `phing checkout -Dtagname ssexpress-0.1.0 -DincludeBaseDir yes`
* Create the archive `phing archive -Dversion ssexpress-0.1.0 -Darchivedest releases -Darchivename ssexpress-0.1.0 -Darchivetype tar.gz`
* Checkout the latest version again: `phing checkout -Dtagname ssexpress -DincludeBaseDir yes`

### Prerequisites

`Phing`:

* `sudo pear channel-discover pear.phing.info`
* `sudo pear install --alldeps phing/phing`

`VersionControl Git`:

* `sudo pear install VersionControl_Git-0.4.4`
