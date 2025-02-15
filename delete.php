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
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $employee = deleteEmployee($pdo, $employeeID);
        if (!$employee) {
            $errorMessage = 'There was an error while deleting the employee';
        } else {
            header('Location: index.php');
        }
    }
}

$pageTitle = 'Delete employee';
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
                <section>
                    <p>Are you sure that you want to delete the following employee?</p>
                </section>
                <p><strong>First name: </strong><?=$employee['cFirstName'] ?></p>
                <p><strong>Last name: </strong><?=$employee['cLastName'] ?></p>
                <p><strong>Email address: </strong><?=$employee['cEmail'] ?></p>
                <p><strong>Birth date: </strong><?=$employee['dBirth'] ?></p>
                <p><strong>Department: </strong><?=$employee['cName'] ?></p>
            <?php endif; ?>
        </section>
        <form action="delete.php?id=<?=$employeeID ?>" method="POST">
            <button type="submit">Delete employee</button>
        </form>
    </main>

<?php include 'public/footer.php'; ?>