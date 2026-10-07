<?php
/**
 * Export Current ExpoTree Database to Clean SQL File for Hostinger
 * Target Database: u424679052_expotree
 */
require_once __DIR__ . '/../includes/db.php';

$outputFile = __DIR__ . '/../database/hostinger_u424679052_expotree.sql';
$fp = fopen($outputFile, 'w');

if (!$fp) {
    die("Cannot open output file: $outputFile\n");
}

$header = "-- ==========================================================\n"
        . "-- Expo Tree Exhibitions Production Database Dump\n"
        . "-- Target Hostinger Database: u424679052_expotree\n"
        . "-- Generated: " . date('Y-m-d H:i:s') . "\n"
        . "-- ==========================================================\n\n"
        . "SET FOREIGN_KEY_CHECKS = 0;\n"
        . "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n"
        . "SET NAMES utf8mb4;\n\n";

fwrite($fp, $header);

$tables = ['admin_users', 'events', 'event_stalls', 'bookings', 'shopper_passes', 'site_settings'];

foreach ($tables as $table) {
    echo "Exporting table: $table...\n";
    
    // Drop table if exists
    fwrite($fp, "-- ----------------------------------------------------------\n");
    fwrite($fp, "-- Table structure for `$table`\n");
    fwrite($fp, "-- ----------------------------------------------------------\n");
    fwrite($fp, "DROP TABLE IF EXISTS `$table`;\n");
    
    // Create table syntax
    $createStmt = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
    $createSql = $createStmt['Create Table'] ?? '';
    fwrite($fp, $createSql . ";\n\n");
    
    // Data
    $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($rows)) {
        fwrite($fp, "-- Dumping data for `$table` (" . count($rows) . " rows)\n");
        $cols = array_keys($rows[0]);
        $escapedCols = array_map(function($c) { return "`$c`"; }, $cols);
        $colList = implode(', ', $escapedCols);
        
        $insertPrefix = "INSERT INTO `$table` ($colList) VALUES\n";
        $valChunks = [];
        
        foreach ($rows as $row) {
            $escapedVals = [];
            foreach ($row as $val) {
                if ($val === null) {
                    $escapedVals[] = 'NULL';
                } else {
                    $escapedVals[] = $pdo->quote($val);
                }
            }
            $valChunks[] = "(" . implode(', ', $escapedVals) . ")";
        }
        
        // Chunk into groups of 50
        $chunkSize = 50;
        for ($i = 0; $i < count($valChunks); $i += $chunkSize) {
            $slice = array_slice($valChunks, $i, $chunkSize);
            fwrite($fp, "INSERT INTO `$table` ($colList) VALUES\n" . implode(",\n", $slice) . ";\n\n");
        }
    }
}

fwrite($fp, "SET FOREIGN_KEY_CHECKS = 1;\n");
fwrite($fp, "-- End of dump\n");
fclose($fp);

echo "Successfully exported database to: $outputFile (" . filesize($outputFile) . " bytes)\n";
