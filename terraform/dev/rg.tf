resource "azurerm_resource_group" "itof-dev-weu-01-rg" {
  name     = "${var.itof_dev_az_prefix}-itof-dev-weu-01-rg"
  location = var.itof_dev_location
}
