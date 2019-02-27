# Hire In Social

[![Build Status](https://travis-ci.org/norzechowicz/hire-in-social.svg?branch=master)](https://travis-ci.org/norzechowicz/hire-in-social)

Hire in Social focuses mostly on posting quick job offers for IT talents (but not only) and propagate everything
over social media. No hidden fees, transparent rules, everything to save precious time of candidates.

Transparency is the key to quality, that's why this project is developed and published as an open source. 

## Roadmap

If you want to help, please check the project roadmap.

[Hire in Social - development](https://github.com/norzechowicz/hire-in-social/projects/1)

## Releases

*There was no stable release yet, things might still be changed* 

* [![Build Status](https://travis-ci.org/norzechowicz/hire-in-social.svg?branch=master)](https://travis-ci.org/norzechowicz/hire-in-social) - development (master)

## Supported channels 

 * Facebook 
    * Group 
        * Post offer as a Page that is linked to a Group

## Requirements

| Service       | Version       |
| ------------- | ------------- |
| OS            | Ubuntu 16.04  |
| PHP           | 7.2           |
| Redis         | 3.0.6         |
| Nginx         | 1.10.3        |

## Configuration

Check [.env.dist file](php/hireinsocial/.env.dist)
 
## Development

Read [Vagrant Readme](vagrant/README.md)

Vagrant environment is setup by ansible playbooks developed under [Iroquois organization](https://github.com/iroquoisorg)

Read [PHP Readme](php/hireinsocial/README.md)

## Facebook 

In order to use Facebook as a social media destination channel you need to meet following requirements:

1) Create Facebook Page
2) Create Facebook Group and link Page from previous step with it
3) Obtain never expiring Facebook Access Token for Page with following permissions:
    * publish_pages (required to create never expiring access token)
    * manage_pages (required to create never expiring access token)
    * publish_to_groups
4) Create Facebook App at developers.facebook.com 
    
https://sujipthapa.co/blog/generating-never-expiring-facebook-page-access-token 

