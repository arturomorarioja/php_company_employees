<?php

require_once 'src/database.php';
require_once 'src/employees.php';

$errorMessage = '';

$pdo = connect();
if (!$pdo) {
    $errorMessage = 'There was an error while connecting to the database.';
} else {
    $employeeID = (int) ($_GET['id'] ?? 0);

    if ($employeeID === 0) {
        header('Location: index.php');
        exit;
    }

    $employee = getEmployeeByID($pdo, $employeeID);
    if (!$employee) {
        $errorMessage = 'There was an error while retrieving employee information';
    }
}

$pageTitle = 'Employee';
include 'public/header.php';

?>

    <nav class="nav">
        <ul>
            <li><a href="index.php" title="Homepage">Back</a></li>
        </ul>
    </nav>
    <main>
        <section>
            <?php if ($errorMessage): ?>
                    <p><?=$errorMessage ?></p>
            <?php else: ?>
                <p><strong>First name: </strong><?=$employee['cFirstName'] ?></p>
                <p><strong>Last name: </strong><?=$employee['cLastName'] ?></p>
                <p><strong>Email address: </strong><?=$employee['cEmail'] ?></p>
                <p><strong>Birth date: </strong><?=$employee['dBirth'] ?></p>
                <p><strong>Department: </strong><?=$employee['cName'] ?></p>
            <?php endif; ?>
        </section>
    </main>

<?php include 'public/footer.php'; ?>