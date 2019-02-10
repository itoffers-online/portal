# Hire In Social

Main goal of this system is to allow people to post job offers on your social media channels without moderation. 

## Roadmap

[Hire in Social - development](https://github.com/norzechowicz/hire-in-social/projects/1)

Roadmap of the project and todo list is hosted on Github Projects in this repository. 

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

