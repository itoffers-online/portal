## Facebook 

[↩️ back](/php/portal/docs/README.md)

In order to use Facebook as a social media destination channel you need to meet following requirements:

1) Create Facebook Page
2) Create Facebook Group and link Page from previous step with it
3) Obtain never expiring Facebook Access Token for Page with following permissions:
    * publish_pages (required to create never expiring access token)
    * manage_pages (required to create never expiring access token)
    * publish_to_groups
4) Create Facebook App at developers.facebook.com 
    
[long living token guide](https://sujipthapa.co/blog/generating-never-expiring-facebook-page-access-token)

Problem with manage_pages and publish_pages is that Facebook needs to approve those permission before app can go live.
However when app is in dev mode, owner of the app can use those permissions without any restrictions. 

Because of that itoffers uses 2 facebook apps, one that is live and second that is test instance of the live one. 

FB Apps:

1) Internal - to post offers at groups
2) External - to let recruiters log in into the system

---
[↩️ back](/php/portal/docs/README.md) 