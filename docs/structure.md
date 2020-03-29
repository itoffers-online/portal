# Repository Structure

[↩️ back](/README.md)

Hire in Social is a monolith repository, it holds all technologies, tools, source code and literally everything 
you as a developer might need. Please find an explanation about how to navigate across the project below. 

### Structure

```
.
├── LICENSE.md
├── README.md
├── ansible
│   └── vagrant
├── docs
│   ├── architecture
│   ├── requirements.md
│   └── structure.md
├── php
│   └── hireinsocial
├── ssl
│   ├── README.md
│   └── ca.crt
└── vagrant
    ├── README.md
    ├── Vagrantfile
    └── Vagrantfile.dist
``` 

(generated with [tree command](https://linux.die.net/man/1/tree))

In most of those folders you will find additional explanation in markdown files. 

#### ansible

[Ansible](https://www.ansible.com/) playbooks used to:

* Provision Vagrant
* Deploy 

### docs

Documentation, keeps things mentioned in [README.md](/README.md)

### php/hireinsocial

PHP part of the project source code.

### ssl 

This is where Local Root CA is going to be generated after `vagrant provision`, it's used to generated SSL certicate
for local development and can be used to make that certificate trusted in your browser. 

### vagrant

[Vagrant](https://www.vagrantup.com/) configuration used to launch local development environment.

---
[↩️ back](/README.md) 