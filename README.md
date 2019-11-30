# Hire In Social

Hire in Social focuses mostly on posting quick job offers for IT talents (but not only) and propagate everything
over social media. No hidden fees, transparent rules, everything to save precious time of candidates.

Transparency is the key to quality, that's why this project is developed and published as an open source. 

## Architecture

[Structurizr Workspace](https://structurizr.com/public/49192)

![Architecture](https://structurizr.com/share/49192/images/system-landscape.png)

![Key](https://structurizr.com/share/49192/images/system-landscape-key.png)

## Roadmap

If you want to help, please check the project roadmap.

[Hire in Social - development](https://github.com/norzechowicz/hire-in-social/projects/1)

## Releases

*There was no stable release yet, things might still be changed* 

## Supported channels 

 * Website
    * Post offer at hire in social web page 
 * Facebook 
    * Group 
        * Post offer as a Page that is linked to a Group

## Requirements

| Service       | Version       |
| ------------- | ------------- |
| OS            | Ubuntu 18.04  |
| PHP           | 7.3           |
| Redis         | 3.0.6         |
| Nginx         | 1.10.3        |
| PostgreSQL    | 11.2          |

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

