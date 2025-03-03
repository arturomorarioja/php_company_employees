<?php

$errorMessage = '';

require_once '../../initialise.php';
require_once ROOT_PATH . '/classes/Department.php';
$department = new Department();
if ($department->connexionError) {
    $errorMessage = 'There was an error while connecting to the database.';
} else {
    $departmentID = (int) ($_GET['id'] ?? 0);

    if ($departmentID === 0) {
        header('Location: index.php');
        exit;
    }

    $departmentToDelete = $department->getByID($departmentID);
    if (!$departmentToDelete) {
        $errorMessage = 'There was an error while retrieving department information';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $department = $department->delete($departmentID);
        if (!$department) {
            $errorMessage = 'There was an error while deleting the department';
        } else {
            if (gettype($department) === 'string') {
                $errorMessage = $department;
            } else {
                header('Location: index.php');
            }
        }
    }
}

$pageTitle = 'Delete department';
include ROOT_PATH . '/public/header.php';
include ROOT_PATH . '/public/nav.php';

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
                    <p>Are you sure that you want to delete the following department?</p>
                </section>
                <p><strong>Name: </strong><?=htmlspecialchars($departmentToDelete[0]['department_name']) ?></p>
                <section id="employees">
                    <header>
                        <h2><strong>Employees:</strong></h2>
                    </header>
                    <ul>
                        <?php foreach ($departmentToDelete as $employee): ?>
                            <?php if ($employee['first_name'] === null): ?>
                                <p>No employees assigned to this department yet.</p>
                            <?php else: ?>
                                <li><?=$employee['last_name'] . ', ' . $employee['first_name'] . ' (' . $employee['department_name'] . ')' ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </section>
                <form action="delete.php?id=<?=$departmentID ?>" method="POST">
                    <button type="submit">Delete department</button>
                </form>
            <?php endif; ?>
        </section>
    </main>

<?php include ROOT_PATH . '/public/footer.php'; ?>