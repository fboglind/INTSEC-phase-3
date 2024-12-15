<?php
echo "<pre>";

// Function to check if a port is open
function check_port($port) {
    $connection = @fsockopen('localhost', $port, $errno, $errstr, 1);
    if (is_resource($connection)) {
        echo "Port $port is open\n";
        
        // Try to get banner
        $banner = fgets($connection);
        if ($banner) {
            echo "Banner: $banner\n";
        }
        
        fclose($connection);
        return true;
    }
    return false;
}

echo "Starting port scan...\n";

// Scan common ports
$common_ports = array(
    20, 21,     // FTP
    22,         // SSH
    23,         // Telnet
    25,         // SMTP
    80, 443,    // HTTP/HTTPS
    3306,       // MySQL
    3002,       // Crypto Helper
    8080,       // Alternative HTTP
    8000,       // Common development port
    9000        // Common development port
);

foreach ($common_ports as $port) {
    check_port($port);
}

echo "\nScan complete!\n";
echo "</pre>";
?>
