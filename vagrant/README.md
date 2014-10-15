Testcenter Server Vagrant VMs CHANGELOG
=======================================

Used to create VMs required for testing the TestCenter Application Web Services,
UI Client and Database.

It also includes a 'builder' VM that is currently used to build PHP 5 modules
(like PHALCON) to be used within the 'webserver' VM.

Requirements
------------

VAGRANT:
- Vagrant 1.5.0 or better
- Vagrant LXC Plugin 1.0.0.alpha.3 or better
- Vagrant Cachier Plugin 1.0.0 or better
- Vagrant Omnibus Plugin 1.4.1 or better

Please Note:
Using 'vagrant plugin install vagrant-lxc' might not install the required
version (1.0.0.alpha.3 or better).

If required, please refer to https://github.com/fgrehm/vagrant-lxc for instructions
on installing the correct version.

CHEF:
Both the 'database' and 'webserver' VMs CHEF recipes hav requirements on the
following community CHEF recipes:
- apt
- mysql
-- yum-mysql-community
-- yum

Usage
-----

Step 1: Build a BASE BOX to be used in the other VMs
PLEASE REFER TO: https://github.com/fgrehm/vagrant-lxc-base-boxes
for a script that can help in building the base boxes.
NOTE: The VMs are being built against a Ubuntu 14.04 Base Box

Example (in my case)
bash$ cd ~/vagrant/lxc/vagrant-lxc-base-boxes/
bash$ make trusty
bash$ cd output/2014-10-01
bash$ vagrant box add --name local-trusty64-20141001 vagrant-lxc-trusty-amd64.box

Step 2: Modify the variable in the 'Vagrantfile'
a) Modify the Variable used to set the Base Box used for the VMs.
Example: In my case
# BASE Box for VMs
BASE_BOX = "local-trusty64-20141001"

b) Modify the Network IPs used for the VMs, to reflect the LXC Network in you installation:

Example: In my case
# DATABASE VM
database.vm.provider :lxc do |lxc|
  lxc.customize 'network.ipv4', '10.193.0.10/24'
end

# WEBSERVER VM
webserver.vm.provider :lxc do |lxc|
  lxc.customize 'network.ipv4', '10.193.0.20/24'
end

Step 2: Prepare for CHEF Use
a) use 'knife' to download recipe dependencies, listed above.
ex: bash$ knife cookbook site download apt, mysql, yum, yum-mysql-community

NOTE: you will probably have to extract/unzip the files into the cookbook directoy
Example Cookbooks Directory structure:
cookbooks/
├── apt
├── builder
├── database
├── mysql
├── webserver
├── yum
└── yum-mysql-community

Step 4: Build Initial Version of PHALCON Module
a) from command line do
bash$ vagrant up --provider=lxc builder

b) (OPTIONAL) bash$ vagrant destroy builder
As it is no longer required.


Step 5: Create and Provision Database VM
a) Modify the attributes of the database recipe as per requirements, normally
found in:
./cookbooks/database/attributes/default.rb

b) Create and Provision the VM
bash$ vagrant up --provider=lxc database

Step 6: Create and Provision Webserver VM
a) Modify attributes for WEBSERVER as required, most notably, enable/dsiable
XDEBUG module and if XDEBUG is used, set the correct remote host IP (i.e. the
ip for lxc bridge interface,, in my case lxcbr0).

b) Modfiy the configurations files for the client and service applications
- Client UI :
file ... /testcenter/client/shared/source/index.php (Web Services IP and if necessary URLs)

- Web Services :
file ... /testcenter/services/web/private/config/config.php (Modify Database Server IP Addresses)

c) Create and Provision the VM
bash$ vagrant up --provider=lxc webserver

License and Authors
-------------------
UNLESS A FILE SPECIFICALLY STATES OTHERWISE (example: Database Schema and Load
Files for TestCenvet ./database/*.sql, works from different authors, etc.)
ALL WORKS INCLUDED IN ARE LICENSED UNDER THE MIT LICENSE as STATED BELOW

**
The MIT License (MIT)

Copyright (c) 2014 "Paulo Ferreira" pf@sourcenotes.org

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
**
