#
# Cookbook Name:: phalcon
# Recipe:: default
#
# Copyright 2014, Paulo Ferreira
#
# All rights reserved - Do Not Redistribute
#

# Make Path to Share Modules with the Other VMs Exist
directory node[:modules][:savepath] do
  owner 'root'
  group 'root'
  mode '0777'
  recursive true
  action :create
  not_if "test -d #{node[:modules][:savepath]}"
end

# Add Required Packages
node['builder']['packages'].each do |phalcon_package|
  package phalcon_package do
    action :install
  end
end

# Make Sure PHALCON Build Path Exists
directory node[:phalcon][:buildpath] do
  owner 'root'
  group 'root'
  mode '0755'
  recursive true
  action :create
  not_if "test -d #{node[:phalcon][:buildpath]}"
end

# Clone PHALCON Repository
execute "git clone --depth=1 #{node[:phalcon][:giturl]}" do
  cwd node[:phalcon][:buildpath]
  not_if "test -d #{node[:phalcon][:buildpath]}/cphalcon"
end

# Build PHALCON
execute "./install" do
  cwd "#{node[:phalcon][:buildpath]}/cphalcon/build"
  only_if "test -d #{node[:phalcon][:buildpath]}/cphalcon/build"
end

# Copy PHALCON Module and INI to Save Path
execute "cp phalcon.so #{node[:modules][:savepath]}" do
  cwd "#{node[:phalcon][:buildpath]}/cphalcon/build/64bits/modules/"
end

template "#{node[:modules][:savepath]}/phalcon.ini" do
  source 'phalcon.ini.erb'
  owner 'root'
  group 'root'
  mode '0644'
end
