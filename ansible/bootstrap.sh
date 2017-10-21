#!/usr/bin/env bash

# update repositories
sudo apt-get update

# manage the repositories that you install software from (common)
sudo apt-get install -y software-properties-common

# add ansible repository & install ansible
sudo add-apt-repository -y ppa:ansible/ansible
sudo apt-get update
sudo apt-get install -y ansible

# copy ssh key
cat /vagrant/ansible/files/authorized_keys >> /home/vagrant/.ssh/authorized_keys

# make ansible-playbook's output visible during run, not only afterwards.
# see https://groups.google.com/d/msg/ansible-project/aXutTmXAbR0/bpVaZwqIhhYJ
# We need to do this, because via nfs all files are +x, so we cannot use a simple custom inventory path
# see http://stackoverflow.com/questions/26859360/cant-use-ansible-inventory-file-because-it-is-executable
sudo PYTHONUNBUFFERED=1 ansible-playbook --inventory-file=/vagrant/ansible/inventories/development -e hostname=$1 --connection=local --limit vagrant /vagrant/ansible/playbook.yml -vvvv
