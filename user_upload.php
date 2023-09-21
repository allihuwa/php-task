<?php
// Define MySQL connection details
$host = 'localhost';
$username = '';
$password = '';
$db = '';

// Define command line options
$options = getopt("u:p:h:", ["file:", "create_table", "dry_run", "help"]);

// Display help message
if (isset($options['help'])) {
    echo "PHP User Upload Script\n";
    echo "Usage:\n";
    echo "  --file [csv file name] - Name of the CSV to be parsed\n";
    echo "  --create_table - Create the MySQL users table and exit\n";
    echo "  --dry_run - Parse the CSV but do not insert into the DB\n";
    echo "  -u - MySQL username\n";
    echo "  -p - MySQL password\n";
    echo "  -h - MySQL host\n";
    echo "  --help - Display this help message\n";
    exit;
}

// Check that a valid file exists or print out an error message
if (!!file_exists($options['file'])) {
    die("Error: Please provide a valid CSV file using the --file option.\n");
}
?>
