<?php
echo "<pre>";

// Function to list directory contents with permissions
function list_directory($path, $level = 0) {
    // Create indentation based on level
    $indent = str_repeat("  ", $level);
    
    echo "$indent📁 $path\n";
    
    // Get directory contents
    $command = "ls -la " . escapeshellarg($path) . " 2>/dev/null";
    $output = shell_exec($command);
    
    if ($output) {
        echo $indent . str_replace("\n", "\n$indent", $output) . "\n";
    }
}

// List key directories we're interested in
$directories = array(
    '/passoire',
    '/passoire/web',
    '/passoire/web/uploads',
    '/passoire/crypto-helper',
    '/home',
    '/home/passoire',
    '/var/www',
    '/etc/apache2',
    '/etc/mysql'
);

echo "File System Overview:\n\n";

foreach ($directories as $dir) {
    list_directory($dir);
    echo "\n";
}

// Also show overall directory structure
echo "Directory Tree Structure:\n";
$tree_command = "find /passoire /home/passoire -type d 2>/dev/null | sort";
$tree = shell_exec($tree_command);
echo $tree;

echo "</pre>";
?>
