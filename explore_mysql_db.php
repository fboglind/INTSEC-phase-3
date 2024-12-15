<?php
echo "<pre>";

try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=passoire', 'passoire', 'prince');
    echo "Connected to MySQL!\n\n";
    
    // Tables we want to examine
    $tables = array('files', 'links', 'messages', 'userinfos', 'users');
    
    foreach ($tables as $table) {
        echo "\nExamining table: $table\n";
        echo str_repeat("-", 50) . "\n";
        
        // First show table structure
        $structure = $db->query("DESCRIBE $table");
        echo "Table structure:\n";
        while ($col = $structure->fetch(PDO::FETCH_ASSOC)) {
            echo "- {$col['Field']} ({$col['Type']})\n";
        }
        
        // Then show table content
        $content = $db->query("SELECT * FROM $table");
        echo "\nTable content:\n";
        while ($row = $content->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
        
        // Also try specific search for flag-related content
        $flagSearch = $db->query("SELECT * FROM $table WHERE CONCAT_WS(' ', " . 
            implode(',', array_keys($content->getColumnMeta(0))) . 
            ") LIKE '%flag%'");
        
        if ($flagSearch && $flagSearch->rowCount() > 0) {
            echo "\nFlag-related content found:\n";
            while ($row = $flagSearch->fetch(PDO::FETCH_ASSOC)) {
                print_r($row);
            }
        }
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>
