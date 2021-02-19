#!/bin/bash

echo 'Pushing to Development Environment...'
rsync ./* "sdrtdev@sdrtdev.ssh.wpengine.net:/home/wpe-user/sites/sdrtdev/wp-content/plugins/sdrt-custom-functions/" \
 -azP -e 'ssh -p 22' --delete --delete-excluded --exclude-from="$(pwd)/.distignore"
