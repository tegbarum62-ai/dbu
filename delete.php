<?php
require_once 'db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $stmt = $conn->prepare("SELECT profile_picture FROM students WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    if ($student) {
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        if ($student['profile_picture']) @unlink('uploads/' . $student['profile_picture']);
    }
}
header('Location: students.php?deleted=1');
exit;
