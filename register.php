<?php
require_once 'db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'full_name', 'student_id', 'department', 'email', 'gender',
        'date_of_birth', 'phone', 'address', 'academic_year',
        'year_level', 'semester', 'registration_date'
    ];
    foreach ($fields as $field) {
        ${$field} = trim($_POST[$field] ?? '');
        if (${$field} === '') $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
    }
    if (!filter_var($email ?? '', FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';

    $profileName = null;
    if (!empty($_FILES['profile_picture']['name'])) {
        $file = $_FILES['profile_picture'];
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $type = $file['type'];
        if (!isset($allowed[$type])) {
            $errors[] = 'Profile picture must be JPG, PNG or WEBP.';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Profile picture must be smaller than 2 MB.';
        } elseif ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'The profile picture could not be uploaded.';
        } else {
            if (!is_dir('uploads')) mkdir('uploads', 0777, true);
            $profileName = 'student_' . bin2hex(random_bytes(8)) . '.' . $allowed[$type];
            if (!move_uploaded_file($file['tmp_name'], 'uploads/' . $profileName)) {
                $errors[] = 'The profile picture could not be saved.';
            }
        }
    }

    if (!$errors) {
        try {
            $stmt = $conn->prepare("INSERT INTO students
                (full_name, student_id, department, email, gender, date_of_birth, phone, address,
                 academic_year, year_level, semester, registration_date, profile_picture)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sssssssssssss', $full_name, $student_id, $department, $email, $gender,
                $date_of_birth, $phone, $address, $academic_year, $year_level, $semester,
                $registration_date, $profileName);
            $stmt->execute();
            header('Location: students.php?success=registered');
            exit;
        } catch (mysqli_sql_exception $exception) {
            if ($profileName) @unlink('uploads/' . $profileName);
            $errors[] = 'Student ID or email already exists.';
        }
    }
}

$value = fn($key) => htmlspecialchars($_POST[$key] ?? '');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <a class="brand" href="index.php"><span>S</span><strong>Student Portal</strong></a>
        <nav><a href="index.php">Dashboard</a><a class="active" href="register.php">Add Student</a><a href="students.php">Students</a></nav>
    </aside>
    <main>
        <header class="topbar"><div><p class="eyebrow">Student Information</p><h1>Register Student</h1></div></header>
        <?php if ($errors): ?>
            <div class="alert"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
        <?php endif; ?>
        <form class="card form-grid" method="post" enctype="multipart/form-data">
            <label>Full Name<input name="full_name" value="<?= $value('full_name') ?>" required></label>
            <label>Student ID<input name="student_id" value="<?= $value('student_id') ?>" required></label>
            <label>Department<input name="department" value="<?= $value('department') ?>" required></label>
            <label>Email Address<input type="email" name="email" value="<?= $value('email') ?>" required></label>
            <label>Gender<select name="gender" required>
                <option value="">Select gender</option>
                <?php foreach (['Male', 'Female', 'Other'] as $item): ?>
                    <option <?= ($_POST['gender'] ?? '') === $item ? 'selected' : '' ?>><?= $item ?></option>
                <?php endforeach; ?>
            </select></label>
            <label>Date of Birth<input type="date" name="date_of_birth" value="<?= $value('date_of_birth') ?>" required></label>
            <label>Phone Number<input name="phone" value="<?= $value('phone') ?>" required></label>
            <label>Academic Year<input name="academic_year" placeholder="2025/2026" value="<?= $value('academic_year') ?>" required></label>
            <label>Year Level<select name="year_level" required>
                <option value="">Select year</option>
                <?php foreach (['1st Year','2nd Year','3rd Year','4th Year','5th Year'] as $item): ?>
                    <option <?= ($_POST['year_level'] ?? '') === $item ? 'selected' : '' ?>><?= $item ?></option>
                <?php endforeach; ?>
            </select></label>
            <label>Semester<select name="semester" required>
                <option value="">Select semester</option>
                <?php foreach (['First Semester','Second Semester','Summer'] as $item): ?>
                    <option <?= ($_POST['semester'] ?? '') === $item ? 'selected' : '' ?>><?= $item ?></option>
                <?php endforeach; ?>
            </select></label>
            <label>Registration Date<input type="date" name="registration_date" value="<?= $value('registration_date') ?: date('Y-m-d') ?>" required></label>
            <label>Profile Picture<input type="file" name="profile_picture" accept="image/jpeg,image/png,image/webp"></label>
            <label class="full">Address<textarea name="address" required><?= $value('address') ?></textarea></label>
            <div class="form-actions full"><button class="button primary" type="submit">Register Student</button><a class="button secondary" href="students.php">Cancel</a></div>
        </form>
    </main>
</div>
</body>
</html>
