<?php
// Define default MySQL connection details
$host = 'localhost';
$username = 'root';
$database = 'test';
$password = '';

while (true) {
	echo "Enter a command (type '--help' for help, or 'exit' to quit): ";
	$stdin = trim(fgets(STDIN));
	$command = explode(' ', $stdin);

	// Count number of arguments
	if (count($command) > 2) {
		echo "Too many arguments.\n";
	}
	
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
	}

	// Check that a valid file exists and that the user provides a file name or print out an error message
	elseif ($command[0] === '--file' || $command[0] === '--dry-run') {

		//Check if file exists
		if(!file_exists($command[1])) {
			echo("Error: Please provide a valid CSV file using the --file option.\n");
		}

		// Open and read the CSV file
		$csvFile = fopen($command[1], 'r');
		if ($csvFile === false) {
				echo("Error: Unable to open CSV file.\n");
				break;
		}

		//Sanitize the CSV data for duplicates and incorrect emails
		$uniqueData = sanitizeData($csvFile);

		//Add to database table if not a dry-run
		if($command[0] === '--file') {
			addToDatabase($uniqueData, $host, $username, $password, $database);
		}

		if($command[0] === '--dry-run') {
			
			echo "Printing out sanitzied data for dry run:\n";
			// Iterate through the sanitized data and print each record
			foreach ($uniqueData as $record) {
				$name = $record[0];
				$surname = $record[1];
				$email = $record[2];
				
				echo "Name: $name, Surname: $surname, Email: $email\n";
			}
			echo "Dry run complete.\n";
		}

		// Close resources
		fclose($csvFile);
	}
}

function sanitizeData($filename) {
	$sanitized_data = [];
	// Iterate through CSV rows
	while (($data = fgetcsv($filename)) !== false) {
		$name = ucfirst(strtolower(trim($data[0])));
		$surname = ucfirst(strtolower(trim($data[1])));
		$email = strtolower(trim($data[2]));

		// Validate email format
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				echo "Invalid email format: $email - skipping to next record\n";
				continue; // Skip this record and move to the next
		}

		// Check if the email already exists in $uniqueData
		$existingKeys = array_keys(array_column($sanitized_data, 2), $email);

		if (empty($existingKeys)) {
				// Email doesn't exist in $uniqueData, add the record
				$sanitized_data[] = [$name, $surname, $email];
		} else {
				// Email already exists, report a duplicate
				echo "Duplicate email found: $email\n for user with name $name and surname $surname, skipping record\n";
		}
	}

	echo ("Data succesfully sanitized.\n");
	return $sanitized_data;
}

function addToDatabase($data, $host, $username, $password, $database) {
	$mysqli = new mysqli($host, $username, $password, $database);
	
	// Iterate through the unique data and insert into MySQL table
	foreach ($data as $record) {
		$name = $record[0];
		$surname = $record[1];
		$email = $record[2];

		// Insert data into MySQL table
		$insertQuery = "INSERT INTO users (name, surname, email) VALUES (?, ?, ?)";
		$stmt = $mysqli->prepare($insertQuery);
		$stmt->bind_param("sss", $name, $surname, $email);

		if (!$stmt->execute()) {
				echo "Error inserting data: " . $stmt->error . "\n";
		} else {
				echo "Record inserted successfully: $name, $surname, $email\n";
		}
	}

	$mysqli->close();
}
?>
