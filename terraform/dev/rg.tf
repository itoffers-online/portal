resource "azurerm_resource_group" "his-dev-weu-01-rg" {
  name     = "${var.his_dev_az_prefix}-his-dev-weu-01-rg"
  location = var.his_dev_location
}
