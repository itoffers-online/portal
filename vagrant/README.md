# Vagrant 

Vagrant is recommended for local development, it's using open sourced ansible roles from [Iroquois Organization](https://github.com/iroquoisorg)
The same roles suppose to be used to prepare staging/production environments. 

There are 3 simple steps required to create vagrant virtual machine for this project but first you should make sure
Vagrant and Virtualbox (or any other provider supported by vagrant) are installed at your machine. 


### Prepare Vagrantfile 

`$ cp Vagrantfile.dist Vagrantfile`

Feel free to customize your virtual machine by adding more memory or CPUs. Other settings should stay as they are. 

### Run Vagrant

`$ vagrant up`

At this step vagrant will create virtual machine in Virtualbox (or any other provider) and it will provision it using 
local ansible (installed inside of that machine) using [Vagrant Playbook](../ansible/vagrant/playbook.yml).   

### Hosts 

Now you should add following entry to your `/etc/hosts` if you want to use custom domain instead of IP address
for development. 

```
10.0.0.200  hireinsocial.local
```

This step is recommended but it's also optional, feel free to skip it if you are ok with typing ip address in browser.

### SSL

In order to make your browser happy after successful `vagrant up` you should be able to see `/ssl/ca.crt` file. 
This file is root CA that was generated during provisioning, it's created for development purpose and if you
destroy your machine and create it once again it will be replaced. 
Feel free to import this cert into your local storage (at OSX it's keychain) and make it trusted but do not share it 
with anyone. It's like underwear, it's your and nobody else should wear it. 


### Blackfire

By defualt vagrant should have blackfire installed, all you need to do is register an agent and configure CLI tool.
More details here: https://blackfire.io/docs/up-and-running/installation