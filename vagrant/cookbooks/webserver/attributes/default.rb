# XDEBUG Remote Host IP Address (to allow for remote xdebug connect)
# TODO: Enable or Disable XDEBUG based on variable (i.e. in production or
# pre-production enviroment there is no need for xdebug)
default['xdebug']['enable'] = true
default['xdebug']['remote_host'] = '10.193.0.1'

# Required WebServer Packages in Order of Installation
default['webserver']['packages'] = ['apache2','php5-cli','libapache2-mod-php5','php5-mysql','php5-xdebug', 'php5-dev', 'php-pear', 'libyaml-dev']

# MODULE Save Path
default['modules']['savepath'] = '/vagrant/modules'
