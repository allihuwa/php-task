# CSV to MySQL Data Import Script

This PHP script allows you to process a CSV file and insert its data into a MySQL database. It provides options to create the necessary database table, sanitize and validate data, and avoid duplicate entries.

### Installation

1. Clone the repository to your local machine:

   ```bash
   git clone https://github.com/yourusername/your-repo.git

### Usage

  run the script in a terminal like this: php user_upload.php

  Change the database name as required.
  
  Use any of the following command options: 

  Command Line Options
  The script supports the following command line options:

  --file [csv file name] - Name of the CSV file to be parsed.
  --create_table - Create the MySQL users table and exit.
  --dry_run [csv file name] - Parse the CSV but do not insert into the DB (useful for testing).
  -u - MySQL username.
  -p - MySQL password.
  -h - MySQL host.
  --help - Display help message with a list of directives.

  # Foobar Script

  ### Usage 

  simply run the script:

  ```bash
   php foobar.php
