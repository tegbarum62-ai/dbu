<?php
require_once 'db.php';

$total = $conn->query("SELECT COUNT(*) AS count FROM students")->fetch_assoc()['count'];
$departments = $conn->query("SELECT COUNT(DISTINCT department) AS count FROM students")->fetch_assoc()['count'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Registration System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <a class="brand" href="index.php"><span>S</span><strong>Student Portal</strong></a>
        <nav>
            <a class="active" href="index.php">Dashboard</a>
            <a href="register.php">Add Student</a>
            <a href="students.php">Students</a>
        </nav>
    </aside>
    <main>
        <header class="topbar">
            <div>
                <p class="eyebrow">Administration</p>
                <h1>Dashboard</h1>
            </div>
            <a class="button primary" href="register.php">+ Register Student</a>
        </header>

        <section class="stats">
            <article><span>Students</span><strong><?= (int)$total ?></strong></article>
            <article><span>Departments</span><strong><?= (int)$departments ?></strong></article>
        </section>

        <section class="card welcome">
            <h2>Student Registration System</h2>
            <p>Manage student records, profile pictures, departments and academic information.</p>
            <div class="actions">
                <a class="button primary" href="students.php">View Students</a>
                <a class="button secondary" href="register.php">Add New Student</a>
            </div>
        </section>
    </main>
</div>
</body>
</html>
