<?php
include 'db.php'; // Include your database connection file

if(isset($_FILES["file"]["name"])) {
    $file = $_FILES["file"]["tmp_name"];
    $file_open = fopen($file,"r");

    // Get column names from the first row of the CSV file
    $columns = fgetcsv($file_open);

    // Prepare the column names for the SQL query
    $columnNames = implode(',', array_map(function($column) {
        return "`$column` VARCHAR(255)";
    }, $columns));

    // Create a unique table name based on current timestamp
    $tableName = 'excel_data_' . date('YmdHis');

    // Create the table
    $sql = "CREATE TABLE IF NOT EXISTS $tableName ($columnNames)";
    mysqli_query($conn, $sql);

    // Insert data into the created table
    while(($csv_data = fgetcsv($file_open, 1000, ",")) !== FALSE) {
        $sql = "INSERT INTO $tableName (" . implode(',', $columns) . ") VALUES ('" . implode("','", $csv_data) . "')";
        mysqli_query($conn, $sql);
    }

    fclose($file_open);

    echo "Data imported and table created successfully: $tableName";
}
?>
