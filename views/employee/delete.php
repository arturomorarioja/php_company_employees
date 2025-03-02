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

    $employeeToDelete = $employee->getByID($employeeID);
    if (!$employeeToDelete) {
        $errorMessage = 'There was an error while retrieving employee information';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $employee = $employee->delete($employeeID);
        if (!$employee) {
            $errorMessage = 'There was an error while deleting the employee';
        } else {
            header('Location: index.php');
        }
    }
}

$pageTitle = 'Delete employee';
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
                <section>
                    <p>Are you sure that you want to delete the following employee?</p>
                </section>
                <?php
                    $email = htmlspecialchars($employeeToDelete['email']);
                ?>
                <p><strong>First name: </strong><?=htmlspecialchars($employeeToDelete['first_name']) ?></p>
                <p><strong>Last name: </strong><?=htmlspecialchars($employeeToDelete['last_name']) ?></p>
                <p><strong>Email address: </strong><a href="mailto:<?=$email ?>"><?=$email ?></a></p>
                <p><strong>Birth date: </strong><?=htmlspecialchars($employeeToDelete['birth_date']) ?></p>
                <p><strong>Department: </strong><?=htmlspecialchars($employeeToDelete['department_name']) ?></p>
            <?php endif; ?>
        </section>
        <form action="delete.php?id=<?=$employeeID ?>" method="POST">
            <button type="submit">Delete employee</button>
        </form>
    </main>

<?php include ROOT_PATH . '/public/footer.php'; ?>