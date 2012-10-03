#!/bin/bash

PROJECT=ssexpress

if [ -z $1 ]; then
	echo "Please provide version as the first parameter (e.g. 1.0.8)."
fi

echo "--- Tagging..."
phing tag -Dtagname=$1 -DpushToOrigin=yes

echo "--- Building tarball..."
mkdir _artifacts
phing archive -Darchivedest=_artifacts -Darchivename=$PROJECT-$1 -Darchivetype=tar.gz -Dversion=$1

echo "--- Uploading..."
scp _artifacts/$PROJECT-$1.tar.gz dropnaut:~/deploynaut-resources/builds/$PROJECT/

echo "--- Finished."
