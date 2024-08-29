#!/bin/bash

# Define the base path for fonts
FONT_BASE_PATH="vendor/tecnickcom/tcpdf/fonts"

# List of patterns to delete in the fonts directory
FONT_PATTERNS=(
    "ae_fonts_2.0"
    "dejavu-fonts-ttf-2.33"
    "dejavu-fonts-ttf-2.34"
    "freefont-20100919"
    "freefont-20120503"
    "freemon*"
    "cid*"
    "courier*"
    "aefurat*"
    "dejavusans*"
    "dejavusansb*"
    "dejavusansi*"
    "dejavusansmono*"
    "dejavusanscondensed*"
    "dejavusansextralight*"
    "dejavuserif*"
    "freesans*"
    "freesansi*"
    "freesansb*"
    "freeserif*"
    "freeserifb*"
    "freeserifi*"
    "pdf*"
    "times*"
    "uni2cid*"
)

# Loop through the patterns and delete matching items in the fonts directory
for PATTERN in "${FONT_PATTERNS[@]}"; do
    find "$FONT_BASE_PATH" -name "$PATTERN" -exec rm -rf {} +
done

echo "Specified font items have been deleted."

# Define the path for the images directory
IMAGES_PATH="vendor/tecnickcom/tcpdf/examples/images"

# Delete all files inside the images directory
if [ -d "$IMAGES_PATH" ]; then
    rm -rf "$IMAGES_PATH"/*
    echo "All files inside $IMAGES_PATH have been deleted."
else
    echo "Directory $IMAGES_PATH does not exist."
fi