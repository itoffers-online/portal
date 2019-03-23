resource "azurerm_storage_account" "his-dev-weu-01-stg" {
  name                     = "${var.his_dev_az_prefix}hisdevweu01stg"
  resource_group_name      = "${azurerm_resource_group.his-dev-weu-01-rg.name}"
  location                 = "${var.his_dev_location}"
  account_tier             = "Standard"
  account_replication_type = "LRS"
  account_kind             = "Storage"
}

resource "azurerm_storage_container" "his-dev-weu-01-stc" {
  name                  = "${var.his_dev_az_prefix}-his-dev-weu-01-stc-blob"
  resource_group_name   = "${azurerm_resource_group.his-dev-weu-01-rg.name}"
  storage_account_name  = "${azurerm_storage_account.his-dev-weu-01-stg.name}"
  container_access_type = "blob"
}

output "storage.account.container" {
  value = "${azurerm_storage_container.his-dev-weu-01-stc.name}"
}

output "storage.account.name" {
  value = "${azurerm_storage_account.his-dev-weu-01-stg.name}"
}

output "storage.account.key" {
  value = "${azurerm_storage_account.his-dev-weu-01-stg.primary_access_key}"
}

output "storage.blob.url" {
  value = "${azurerm_storage_container.his-dev-weu-01-stc.id}"
}
