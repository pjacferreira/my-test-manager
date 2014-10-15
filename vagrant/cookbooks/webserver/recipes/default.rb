# Use Recipes to Install MySQL (Client Only)
include_recipe 'mysql::client'

#
# Install Required Packages
#
node['webserver']['packages'].each do |phalcon_package|
  package phalcon_package do
    action :install
  end
end

# Enable Remote Debugging for xdebug
template "/etc/php5/mods-available/xdebug.ini" do
  source 'xdebug.ini.erb'
  owner 'root'
  group 'root'
  mode 0644
end

# Build PHP YAML Module
execute 'pecl install yaml; exit 0;'

# Enable YAML Module
template "/etc/php5/mods-available/yaml.ini" do
  source 'yaml.ini.erb'
  owner 'root'
  group 'root'
  mode 0644
end

# Enable PHP5 Phalcon Module
execute 'php5enmod enable yaml; exit 0;'

#
# Install PHALCON Module
#

# Install PHALCON Module
remote_file 'copy PHALCON.SO' do
  path '/usr/lib/php5/20121212/phalcon.so'
  source "file://#{node[:modules][:savepath]}/phalcon.so"
  owner 'root'
  group 'root'
  mode 0644
end

# Copy PHALCON INI
remote_file 'copy PHALCON.INI' do
  path '/etc/php5/mods-available/phalcon.ini'
  source "file://#{node[:modules][:savepath]}/phalcon.ini"
  owner 'root'
  group 'root'
  mode 0644
end

# Enable PHP5 Phalcon Module
execute 'php5enmod enable phalcon; exit 0;'

# Enable Module Rewrite
execute "a2enmod rewrite"

# Enable MOD-REWRITE on TestCenter Directory
template "/etc/apache2/conf-enabled/testcenter.conf" do
  source 'testcenter.conf.erb'
  owner 'root'
  group 'root'
  mode 0644
end

# Enable Temporary Directory for PHP Session Cookies Sharing
directory "/var/tmp/testcenter" do
  owner 'www-data'
  group 'www-data'
  mode  0770
  action :create
end

# Enable MOD-REWRITE on TestCenter Directory
template "/var/www/html/index.html" do
  source 'index.html.erb'
  owner 'www-data'
  group 'www-data'
  mode 0644
end

#
# TEST PURPOSES : Copy These Files to Apache Document Root
#
template "/var/www/html/index.html" do
  source 'index.html.erb'
  owner 'www-data'
  group 'www-data'
  mode 0644
end

template "/var/www/html/phpinfo.php" do
  source 'phpinfo.php.erb'
  owner 'www-data'
  group 'www-data'
  mode 0644
end

# Make sure Apache is Started and Enabled for boot
service 'apache2' do
  action [:restart, :enable]
end

# Install MC for Debug VM Purposes
package 'mc'

# Cleanup
execute 'apt-get autoremove -y'
