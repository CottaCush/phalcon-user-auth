#!/usr/bin/env bash

function run(){

git checkout develop

git pull origin develop

git fetch

latest_git_commit_id=`git rev-list --tags --max-count=1`
current_version=`git describe --tags ${latest_git_commit_id}`
echo "Please enter release version (current version is $current_version):"

read release_version

script_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
php ${script_dir}/bump_composer_version.php ${release_version}

git add composer.json

git commit -m "bump composer.json version to $release_version"

git push origin develop

git checkout master

git pull origin master

git merge develop

git push origin master

echo "Please enter tag message for $release_version"

read tag_message

git tag -a ${release_version} -m "${tag_message}"

git push --tags

git checkout develop

latest_git_commit_id=`git rev-list --tags --max-count=1`

today=`date +'%Y-%m-%d'`
echo -e "\n# [$release_version](https://bitbucket.org/cottacush/user-auth/src/$latest_git_commit_id/?at=$release_version) ($today)" >> ${script_dir}/../CHANGELOG.md

echo "Please add release change logs"

if hash sublime 2>/dev/null; then
    sublime -w ${script_dir}/../CHANGELOG.md
else
    vim ${script_dir}/../CHANGELOG.md
fi

git add CHANGELOG.md

git commit -m "Add changelog for $release_version"

git push origin develop

git checkout master

git pull origin master

git merge develop

git push origin master

git checkout develop

git diff  --no-ext-diff --unified=0 --exit-code -a --no-prefix ${latest_git_commit_id}..HEAD ${script_dir}/../CHANGELOG.md | egrep "^\+" >> ${script_dir}/slack_update.temp
sed  's .  ' ${script_dir}/slack_update.temp > ${script_dir}/slack_update_2.temp
tail -n +3 ${script_dir}/slack_update_2.temp > ${script_dir}/slack_update.temp
slack_update=`cat ${script_dir}/slack_update.temp`
rm ${script_dir}/slack*

PAYLOAD="payload={\"channel\": \"#terra\", \"username\": \"User Auth Release Bot\", \"text\": \"User Auth $release_version released \n\n $slack_update\", \"icon_emoji\": \":rat:\"}";
curl -s -S -X POST --data-urlencode "$PAYLOAD" https://hooks.slack.com/services/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

echo " "

echo "Release done"

}

run