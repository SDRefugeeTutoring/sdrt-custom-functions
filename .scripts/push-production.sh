#!/bin/bash

read -rp "Are you sure you want to push to production? (y/n) " answer

if [ "$answer" != 'y' ] && [ "$answer" != 'Y' ];
then
  exit 1
fi

echo 'Pushing to Production Environment...'
rsync ./* "sdrt@sdrt.ssh.wpengine.net:/home/wpe-user/sites/sdrt/wp-content/plugins/sdrt-custom-functions/" \
 -azP -e 'ssh -p 22' --delete --delete-excluded --exclude-from="$(pwd)/.distignore"
