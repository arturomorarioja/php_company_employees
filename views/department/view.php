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

    $department = $department->getByID($departmentID);
    if (!$department) {
        $errorMessage = 'There was an error while retrieving department information';
    }
}

$pageTitle = 'Department';
include ROOT_PATH . '/public/header.php';
include ROOT_PATH . '/public/nav.php';
include ROOT_PATH . '/public/nav_back.php';

?>

    <main>
        <section>
            <?php if ($errorMessage): ?>
                <p><?=$errorMessage ?></p>
            <?php else: ?>
                <p><strong>Name: </strong><?=htmlspecialchars($department[0]['department_name']) ?></p>
                <section id="employees">
                    <header>
                        <h2><strong>Employees:</strong></h2>
                    </header>
                    <ul>
                        <?php foreach ($department as $employee): ?>
                            <?php if ($employee['first_name'] === null): ?>
                                <p>No employees assigned to this department yet.</p>
                            <?php else: ?>
                                <li>
                                    <a 
                                        href="<?=BASE_URL . '/views/employee/view.php?id=' . 
                                            $employee['employee_id'] ?>"><?=$employee['last_name'] . 
                                            ', ' . $employee['first_name'] ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>
        </section>
    </main>

<?php include ROOT_PATH . '/public/footer.php'; ?>