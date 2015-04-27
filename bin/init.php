<?php

// create required folders
function dirExists($path)
{
    if (!file_exists($path) && !is_dir($path)) {
        return false;
    } else {
        return true;
    }
}

function buildDir($path)
{
    if (!dirExists($path)) {
        if (!mkdir($path)) {
            trigger_error('could not create ' . $path . ' folder, create it manually');
        }
    }
}

buildDir('../temp');
buildDir('../log');
buildDir('../www/upload');
buildDir('../www/temp');

