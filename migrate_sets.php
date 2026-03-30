<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

// Add new columns to template_exercises table
$alter_query = "
ALTER TABLE template_exercises
ADD COLUMN set_group INT DEFAULT 1 AFTER day_order,
ADD COLUMN set_order INT DEFAULT 1 AFTER set_group
";

if ($db->query($alter_query)) {
    echo "Successfully added set_group and set_order columns to template_exercises table.\n";

    // Update existing records to have proper set_group values
    $update_query = "
    UPDATE template_exercises
    SET set_group = id, set_order = 1
    WHERE set_group = 1 AND set_order = 1
    ";

    if ($db->query($update_query)) {
        echo "Successfully updated existing records with set grouping.\n";
    } else {
        echo "Error updating existing records: " . $db->error . "\n";
    }
} else {
    echo "Error adding columns: " . $db->error . "\n";
}

$db->close();
?>