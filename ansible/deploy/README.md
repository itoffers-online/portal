# Deployment 

[↩️ back](/README.md)

**This process can't be launched from Vagrant!**

Before starting deployment artifact needs to be prepared, it can be done by executing single composer command: 

```
composer build
```

Next you need to prepare `hosts` file
 
```
cp inventories/hosts.dists inventories/hosts
```

# Staging Deployment
```
ansible-playbook deploy.yml -i inventories/hosts --extra-vars='{"deployhosts":"itof-stag-vm", "artefact_path":"./../../php/itoffers/build/itoffers-archive.tar"}'
```

---
[↩️ back](/README.md)