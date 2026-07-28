## Optional: Prebuilt SQLite DB (Windows)

If you prefer to create the SQLite file directly rather than running the PHP init script, follow these Windows steps:

1) Ensure sqlite3 is available on your PATH. If not, download the SQLite command-line shell for Windows from https://www.sqlite.org/download.html and place sqlite3.exe somewhere on your PATH.

2) From the repository root run these commands in PowerShell or Command Prompt:

```powershell
# Create the data directory if missing
mkdir data

# Import the example SQL into a new SQLite DB file
sqlite3 data\unispace.sqlite < data\unispace.example.sql
```

3) Start the PHP built-in server:

```powershell
php -S localhost:8000
```

4) Open http://localhost:8000/index.php

Notes:
- The sample accounts are:
  - john@example.com / password
  - jane@example.com / password
  - test@example.com / password
- This file (data/unispace.example.sql) is an SQL dump suitable for direct import into sqlite3. If you prefer the PHP init script, run `php scripts/init_sqlite.php` instead.
