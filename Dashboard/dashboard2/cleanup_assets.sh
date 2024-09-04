#!/bin/bash

# Define the assets folder path
assets_folder="assets"

# Extract file paths from the provided HTML content
included_files=(
    "assets/media/logos/favicon.ico"
    "assets/plugins/custom/fullcalendar/fullcalendar.bundle.css"
    "assets/plugins/global/plugins.bundle.css"
    "assets/css/style.bundle.css"
    "assets/plugins/global/plugins.bundle.js"
    "assets/js/scripts.bundle.js"
    "assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"
    "assets/js/custom/widgets.js"
    "assets/js/custom/apps/chat/chat.js"
    "assets/js/custom/modals/create-app.js"
    "assets/js/custom/modals/upgrade-plan.js"
    "assets/js/custom/pages/projects/project/project.js"
    "assets/js/custom/modals/users-search.js"
    "assets/js/custom/modals/new-target.js"
    "assets/js/custom/widgets.js"
    "assets/js/custom/apps/chat/chat.js"
    "assets/js/custom/modals/create-app.js"
    "assets/js/custom/modals/upgrade-plan.js"
)

# Function to recursively list all files in a directory
list_files() {
    find "$1" -type f
}

# List all files in the assets folder
all_files=$(list_files "$assets_folder")

# Delete files not included in the provided HTML content
for file in $all_files; do
    relative_path="${file#./}"
    if [[ ! " ${included_files[@]} " =~ " ${relative_path} " ]]; then
        rm "$file"
        echo "Deleted: $relative_path"
    fi
done