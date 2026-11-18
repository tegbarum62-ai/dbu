<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: students.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: students.php'); exit; }

$fields = ['full_name','student_id','department','email','gender','date_of_birth','phone','address','academic_year','year_level','semester','registration_date'];
$values = [];
$errors = [];
foreach ($fields as $field) {
    $values[$field] = trim($_POST[$field] ?? '');
    if ($values[$field] === '') $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
}
if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';

if ($errors) {
    header('Location: edit.php?id=' . $id . '&error=' . urlencode(implode(' ', $errors)));
    exit;
}

$stmt = $conn->prepare("SELECT profile_picture FROM students WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$old = $stmt->get_result()->fetch_assoc();
if (!$old) { header('Location: students.php'); exit; }

$profileName = $old['profile_picture'];
if (!empty($_FILES['profile_picture']['name'])) {
    $file = $_FILES['profile_picture'];
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$file['type']]) || $file['size'] > 2 * 1024 * 1024 || $file['error'] !== UPLOAD_ERR_OK) {
        header('Location: edit.php?id=' . $id . '&error=' . urlencode('Upload a JPG, PNG or WEBP smaller than 2 MB.'));
        exit;
    }
    $newName = 'student_' . bin2hex(random_bytes(8)) . '.' . $allowed[$file['type']];
    if (!move_uploaded_file($file['tmp_name'], 'uploads/' . $newName)) {
        header('Location: edit.php?id=' . $id . '&error=' . urlencode('The profile picture could not be saved.'));
        exit;
    }
    $profileName = $newName;
}

$sql = "UPDATE students SET full_name=?, student_id=?, department=?, email=?, gender=?,
        date_of_birth=?, phone=?, address=?, academic_year=?, year_level=?, semester=?,
        registration_date=?, profile_picture=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sssssssssssssi', $values['full_name'], $values['student_id'], $values['department'],
    $values['email'], $values['gender'], $values['date_of_birth'], $values['phone'], $values['address'],
    $values['academic_year'], $values['year_level'], $values['semester'], $values['registration_date'],
    $profileName, $id);
try {
    $stmt->execute();
    if (!empty($_FILES['profile_picture']['name']) && $old['profile_picture']) {
        @unlink('uploads/' . $old['profile_picture']);
    }
    header('Location: students.php?success=updated');
    exit;
} catch (mysqli_sql_exception $exception) {
    if (!empty($_FILES['profile_picture']['name'])) @unlink('uploads/' . $profileName);
    header('Location: edit.php?id=' . $id . '&error=' . urlencode('Student ID or email already exists.'));
    exit;
}
