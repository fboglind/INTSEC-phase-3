import base64

# Copy the base64 output from the PHP script into a text file
with open('my_own_cryptographic_algorithm', 'r') as f:
    b64_content = f.read()

# Decode and save as binary
with open('cryptographic_algorithm', 'wb') as f:
    f.write(base64.b64decode(b64_content))
