# Required Database Packages in Order of Installation
default['database']['packages'] = ['lighttpd','php5-cgi','php5-mcrypt']

# Change Defaults for MYSQL
default['mysql']['server_root_password'] = 'ku6dd982'
default['mysql']['allow_remote_root'] = true

# TestCenter Database Attributes
default['testcenter']['db'] = 'testcenter'
