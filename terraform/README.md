# Terraform

[↩️ back](/README.md)

Terraform is used to prepare cloud resources like for example storage. It's not mandatory to use it, it was added in order
to test system locally in prod env. Feel free to skip it if you don't know how to use Terraform.

At this point only Azure provider is supported but feel free to provide others.   

## Setup Azure resources for development purpose

1) Create azure free account
2) Download and install azure cli app and login
3) Download and install terraform

Prepare env variables: 

```
export TF_VAR_his_dev_az_subscription_id="changeme"
export TF_VAR_his_dev_az_tenant_id="changeme"
export TF_VAR_his_dev_az_prefix="YOUR_INITIALS_OR_SOMETHING"
```

---
[↩️ back](/README.md)