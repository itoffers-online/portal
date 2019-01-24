# Deployment 

**This process can't be launched from Vagrant!**

```
cp inventories/hosts.dists inventories/hosts
```

# Staging Deployment
```
ansible-playbook deploy.yml -i inventories/hosts --extra-vars='{"deployhosts":"his-stag-vm", "artefact_path":"./../../php/hireinsocial/build/hireinsocial-archive.tar"}'
```