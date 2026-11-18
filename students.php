<?php
require_once 'db.php';

$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $term = '%' . $search . '%';
    $stmt = $conn->prepare("SELECT * FROM students WHERE full_name LIKE ? OR student_id LIKE ? OR department LIKE ? OR email LIKE ? ORDER BY created_at DESC");
    $stmt->bind_param('ssss', $term, $term, $term, $term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Students</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <a class="brand" href="index.php"><span>S</span><strong>Student Portal</strong></a>
        <nav><a href="index.php">Dashboard</a><a href="register.php">Add Student</a><a class="active" href="students.php">Students</a></nav>
    </aside>
    <main>
        <header class="topbar">
            <div><p class="eyebrow">Records</p><h1>Students</h1></div>
            <a class="button primary" href="register.php">+ Register Student</a>
        </header>
        <?php if (isset($_GET['success'])): ?><div class="alert success">Student record saved successfully.</div><?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?><div class="alert success">Student deleted successfully.</div><?php endif; ?>
        <div class="toolbar card">
            <form method="get" class="search"><input name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search name, ID, department or email"><button class="button secondary">Search</button></form>
        </div>
        <div class="table-card card">
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Student</th><th>Student ID</th><th>Department</th><th>Gender</th><th>Year Level</th><th>Semester</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php if ($result->num_rows === 0): ?>
                        <tr><td colspan="7" class="empty">No students found.</td></tr>
                    <?php endif; ?>
                    <?php while ($student = $result->fetch_assoc()):
                        $picture = $student['profile_picture'] ? 'uploads/' . rawurlencode($student['profile_picture']) : '';
                    ?>
                        <tr>
                            <td><div class="student-cell"><?php if ($picture): ?><img src="<?= htmlspecialchars($picture) ?>" alt=""><?php else: ?><span class="avatar"><?= strtoupper(substr($student['full_name'], 0, 1)) ?></span><?php endif; ?><div><strong><?= htmlspecialchars($student['full_name']) ?></strong><small><?= htmlspecialchars($student['email']) ?></small></div></div></td>
                            <td><?= htmlspecialchars($student['student_id']) ?></td>
                            <td><?= htmlspecialchars($student['department']) ?></td>
                            <td><?= htmlspecialchars($student['gender']) ?></td>
                            <td><?= htmlspecialchars($student['year_level']) ?></td>
                            <td><?= htmlspecialchars($student['semester']) ?></td>
                            <td><div class="row-actions"><a class="edit" href="edit.php?id=<?= (int)$student['id'] ?>">Edit</a><a class="delete" href="delete.php?id=<?= (int)$student['id'] ?>" onclick="return confirm('Delete this student?')">Delete</a></div></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
