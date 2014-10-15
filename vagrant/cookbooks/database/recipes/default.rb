# Use Recipes to Install MySQL
include_recipe 'mysql::client'
include_recipe 'mysql::server'

#
# Install Required Packages
#
node[:database][:packages].each do |pkg|
  package pkg do
    action :install
  end
end

#
# LigHTTPD Enable PHP CGI
#
execute 'lighty-enable-mod fastcgi; exit 0;'
execute 'lighty-enable-mod fastcgi-php; exit 0;'

#
# PHP 5 Configuration
#
execute 'php5enmod mcrypt; exit 0;'

#
# Install PHP MyAdmin
#

# Pre-Install Preparations (Prepare DEBIAN to ANSWER QUESTIONS for PHPMyAdmin package)
execute 'echo \'phpmyadmin phpmyadmin/reconfigure-webserver multiselect lighttpd\' | debconf-set-selections'
execute 'echo \'phpmyadmin phpmyadmin/dbconfig-install boolean true\' | debconf-set-selections'
execute 'echo \'phpmyadmin phpmyadmin/mysql/admin-user string root\' | debconf-set-selections'
execute "echo \'phpmyadmin phpmyadmin/mysql/admin-pass password #{node[:mysql][:server_root_password]}\' | debconf-set-selections"

# Install PHPMyAdmin Packages
# NOTE: We can't use a simple package 'phpmyadmin' because, for some unknown reason
# apt-get install phpmyadmin, in the VM, fails when it tries to do a service lighttpd force-reload.
# This means that, the chef package command fails, and the chef recipe is aborted.
# NOTICE also, that I added an ';exit 0;' exactly for the same reason (this fakes a
# successfull exit of the command, allowing the CHEF recipe to continue)
execute 'install-phpmyadmin' do
  command 'apt-get install -q -y phpmyadmin; exit 0;'
  environment ({'DEBIAN_FRONTEND' => 'noninteractive'})
  action :run
end

# Make sure LigHTTPD is Restarted and Enabled for boot
service 'lighttpd' do
  action [:restart, :enable]
end

# Install MC for Debug VM Purposes
package 'mc'

# Cleanup
execute 'apt-get autoremove -y'

# Initialize Database
execute "mysql --user=root --password=#{node[:mysql][:server_root_password]} < /vagrant/database/database.sql"
execute "mysql --user=root --password=#{node[:mysql][:server_root_password]} #{node[:testcenter][:db]} < /vagrant/database/schema.sql"
execute "mysql --user=root --password=#{node[:mysql][:server_root_password]} #{node[:testcenter][:db]} < /vagrant/database/initial-load.sql"
