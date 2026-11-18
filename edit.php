<?php
require_once 'db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: students.php'); exit; }
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
if (!$student) { header('Location: students.php'); exit; }

$value = fn($key) => htmlspecialchars($_POST[$key] ?? $student[$key]);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <a class="brand" href="index.php"><span>S</span><strong>Student Portal</strong></a>
        <nav><a href="index.php">Dashboard</a><a href="register.php">Add Student</a><a class="active" href="students.php">Students</a></nav>
    </aside>
    <main>
        <header class="topbar"><div><p class="eyebrow">Student Information</p><h1>Edit Student</h1></div></header>
        <?php if (isset($_GET['error'])): ?><div class="alert"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>
        <form class="card form-grid" action="update.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= (int)$id ?>">
            <label>Full Name<input name="full_name" value="<?= $value('full_name') ?>" required></label>
            <label>Student ID<input name="student_id" value="<?= $value('student_id') ?>" required></label>
            <label>Department<input name="department" value="<?= $value('department') ?>" required></label>
            <label>Email Address<input type="email" name="email" value="<?= $value('email') ?>" required></label>
            <label>Gender<select name="gender" required>
                <?php foreach (['Male','Female','Other'] as $item): ?><option <?= $value('gender') === $item ? 'selected' : '' ?>><?= $item ?></option><?php endforeach; ?>
            </select></label>
            <label>Date of Birth<input type="date" name="date_of_birth" value="<?= $value('date_of_birth') ?>" required></label>
            <label>Phone Number<input name="phone" value="<?= $value('phone') ?>" required></label>
            <label>Academic Year<input name="academic_year" value="<?= $value('academic_year') ?>" required></label>
            <label>Year Level<select name="year_level" required>
                <?php foreach (['1st Year','2nd Year','3rd Year','4th Year','5th Year'] as $item): ?><option <?= $value('year_level') === $item ? 'selected' : '' ?>><?= $item ?></option><?php endforeach; ?>
            </select></label>
            <label>Semester<select name="semester" required>
                <?php foreach (['First Semester','Second Semester','Summer'] as $item): ?><option <?= $value('semester') === $item ? 'selected' : '' ?>><?= $item ?></option><?php endforeach; ?>
            </select></label>
            <label>Registration Date<input type="date" name="registration_date" value="<?= $value('registration_date') ?>" required></label>
            <label>Profile Picture<input type="file" name="profile_picture" accept="image/jpeg,image/png,image/webp"></label>
            <label class="full">Address<textarea name="address" required><?= $value('address') ?></textarea></label>
            <div class="form-actions full">
                <?php if ($student['profile_picture']): ?><div class="current-picture"><img src="uploads/<?= htmlspecialchars(rawurlencode($student['profile_picture'])) ?>" alt=""><span>Current picture</span></div><?php endif; ?>
                <button class="button primary" type="submit">Update Student</button><a class="button secondary" href="students.php">Cancel</a>
            </div>
        </form>
    </main>
</div>
</body>
</html>
