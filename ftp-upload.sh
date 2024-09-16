#!/bin/bash

host=$1
user=$2
pass=$3
path=$4
file=$5

create_directories() {
    local dir_path=$1
    local base_path=$2
    IFS='/' read -ra ADDR <<< "$dir_path"
    for i in "${ADDR[@]}"; do
        base_path="$base_path/$i"
        echo "mkdir $base_path"
    done
}

export -f create_directories
export path
export file

{ 
    echo quote USER $user
    echo quote PASS $pass
    echo prompt
    echo mkdir $path
    find $file -mindepth 1 -type d -exec bash -c 'dir_path="${1#$file/}"; echo "mkdir $path/$dir_path"' _ {} \;
    find $file -type f -exec bash -c 'relative_path="${1#$file/}"; echo "put {} $path/$relative_path"' _ {} \;
    echo bye
} | ftp -n $host