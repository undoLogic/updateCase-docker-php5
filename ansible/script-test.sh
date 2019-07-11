#!/usr/bin/env bash
#public can run this to get started
#curl http://install.updatecase.com/cakePHP2 --output install-cakePHP2.sh
#curl https://raw.githubusercontent.com/undoLogic/updateCase-Docker-PHP/master/install-cakePHP-2.sh --output install-cakePHP-2.sh
#This will download the file, then
#chmod install-cakePHP-2.sh
#./install-cakePHP-2.sh

#change to another name if you want
ProjectName="newProject"
svn export https://github.com/undologic/updateCase-Docker-PHP/trunk/. $ProjectName/.
curl -L -o cakephp2.zip https://github.com/cakephp/cakephp/archive/2.x.zip
unzip cakephp2.zip
mv cakephp-2.x/ $ProjectName/back-end/
rm cakephp2.zip