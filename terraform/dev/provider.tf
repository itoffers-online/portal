provider "azurerm" {
  subscription_id = "${var.his_dev_az_subscription_id}"
  tenant_id       = "${var.his_dev_az_tenant_id}"
}
