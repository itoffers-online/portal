# Emails

[↩️ back](/README.md)

Emails are very important part of the whole system, in order to send/read emails system needs to be able to 
access mail server over smtp / imap.  

There are two email accounts that needs to be configured:

* Application Inbox - (read)
* System Mailer - (send)

It's not recommended using one account but from technical point of view, it is possible.  

## Application Inbox

On that email account candidates are going to send their applications, system will read it from there and forward
to recruiter. 

Email scanning should be launched periodically, if possible every 5 minutes.

```
$ bin/email-scan
```

Access Required: IMAP (read)

## System Mailer

When system needs to forward application or send notification it will use System Mailer account. 

By default, system will not send email immediately, instead it will push them into spool that needs to be flushed periodically
by following command:

```
$ bin/symfony swiftmailer:spool:send
```

It's recommended to create a cronjob with that command and execute it every 5 minutes.  

Access Required: SMTP (send)

---
[↩️ back](/README.md) 