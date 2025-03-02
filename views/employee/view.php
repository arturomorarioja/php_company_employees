<?php

$errorMessage = '';

require_once '../../initialise.php';
require_once ROOT_PATH . '/classes/Employee.php';

$employee = new Employee();
if ($employee->connexionError) {
    $errorMessage = 'There was an error while connecting to the database.';
} else {
    
    $employeeID = (int) ($_GET['id'] ?? 0);
    if ($employeeID === 0) {
        header('Location: index.php');
        exit;
    }

    $employee = $employee->getByID($employeeID);
    if (!$employee) {
        $errorMessage = 'There was an error while retrieving employee information';
    }
}

$pageTitle = 'Employee';
include ROOT_PATH . '/public/header.php';

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
                <?php
                    $email = htmlspecialchars($employee['email']);
                ?>
                <p><strong>First name: </strong><?=htmlspecialchars($employee['first_name']) ?></p>
                <p><strong>Last name: </strong><?=htmlspecialchars($employee['last_name']) ?></p>
                <p><strong>Email address: </strong><a href="mailto:<?=$email ?>"><?=$email ?></a></p>
                <p><strong>Birth date: </strong><?=htmlspecialchars($employee['birth_date']) ?></p>
                <p><strong>Department: </strong><?=htmlspecialchars($employee['department_name']) ?></p>
            <?php endif; ?>
        </section>
    </main>

<?php include ROOT_PATH . '/public/footer.php'; ?>