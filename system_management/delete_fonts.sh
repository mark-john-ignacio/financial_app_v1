#!/bin/bash

# Define the base path
BASE_PATH="vendor/tecnickcom/tcpdf/fonts"

# List of patterns to delete
PATTERNS=(
    "freemon*"
    "cid*"
    "courier*"
    "aefurat*"
    "dejavusansb*"
    "dejavusansi*"
    "dejavusansmono*"
    "dejavusanscondensed*"
    "dejavusansextralight*"
    "dejavuserif*"
    "freesansi*"
    "freesansb*"
    "freeserifb*"
    "freeserifi*"
    "pdf*"
    "times*"
    "uni2cid*"
)

# Loop through the patterns and delete matching items
for PATTERN in "${PATTERNS[@]}"; do
    find "$BASE_PATH" -name "$PATTERN" -exec rm -rf {} +
done

echo "Specified items have been deleted."