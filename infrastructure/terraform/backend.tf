terraform {
  backend "gcs" {
    bucket = "laravel-app-terraform-state"
    prefix = "terraform/state"
  }
}
