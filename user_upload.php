<?php
// Define default MySQL connection details
$host = 'localhost';
$username = 'default_username';
$password = 'default_password';
$database = 'default_database';

while (true) {
	echo "Enter a command (type '--help' for help, or 'exit' to quit): ";
	$stdin = trim(fgets(STDIN));
	$command = explode(' ', $stdin);

	// Exit command
	if ($command[0] === 'exit') {
		echo "Goodbye!\n";
		break;
	}

	// Display help message
	elseif ($command[0] === '--help') {
		echo "PHP User Upload Script\n";
		echo "Usage:\n";
		echo "  --file [csv file name] - Name of the CSV to be parsed\n";
		echo "  --create_table - Create the MySQL users table and exit\n";
		echo "  --dry_run - Parse the CSV but do not insert into the DB\n";
		echo "  -u - MySQL username\n";
		echo "  -p - MySQL password\n";
		echo "  -h - MySQL host\n";
		echo "  --help - Display this help message\n";
	}

	// Set MySQL host
	elseif ($command[0] === '-h') {
    $host = $command[1];
	}

	// Set MySQL user
	elseif ($command[0] === '-u') {
			$username = $command[1];
	}

	// Set MySQL password
	elseif ($command[0] === '-p') {
			$password = $command[1];
	}

	// Create or rebuild the MySQL users table if --create_table option is specified
	elseif ($command[0] === '--create_table') {
		$mysqli = new mysqli($host, $username, $password, $database);

		if ($mysqli->connect_error) {
			echo("Connection failed: " . $mysqli->connect_error . "\n");
		}

		$createTableQuery = "
			CREATE TABLE IF NOT EXISTS users (
					id INT AUTO_INCREMENT PRIMARY KEY,
					name VARCHAR(255) NOT NULL,
					surname VARCHAR(255) NOT NULL,
					email VARCHAR(255) NOT NULL UNIQUE
			)";

		if (!$mysqli->query($createTableQuery)) {
			echo("Table creation failed: " . $mysqli->error . "\n");
		}

		echo "MySQL users table created successfully.\n";
		$mysqli->close();
		exit;
	}

	// Check that a valid file exists and that the user provides a file name or print out an error message
	elseif ($command[0] === '--file') {
		if(!file_exists($command[1])) {
			echo("Error: Please provide a valid CSV file using the --file option.\n");
		}
	}
}
?>
