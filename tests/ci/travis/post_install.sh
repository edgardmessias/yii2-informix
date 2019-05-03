#!/bin/bash
#
#  name: post_install.sh:
#  description: Creates the sbspace and test database
#

# Load the Informix environment variables
source "${INFORMIX_HOME}/.bashrc"
source "/opt/ibm/scripts/informix_inf.env"

# Create the sbspace
echo ">>>    Creating sbspace"
touch "${INFORMIX_DATA_DIR}"/spaces/sbspace
chmod 660 "${INFORMIX_DATA_DIR}"/spaces/sbspace
onspaces -c -S sbspace -p "${INFORMIX_DATA_DIR}"/spaces/sbspace -o 0 -s 20000

# Create the test database
echo ">>>    Creating test database"
echo "CREATE DATABASE test WITH BUFFERED LOG" | dbaccess
