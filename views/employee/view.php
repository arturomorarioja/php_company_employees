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

    $projects = $employee->getProjects($employeeID);
    // An empty array evaluates to false, so the return type must be explicitly checked
    if (!$projects && gettype($projects) === 'bool') {  
        $errorMessage = 'There was an error while retrieving project information.';
    }

    $employee = $employee->getByID($employeeID);
    if (!$employee) {
        $errorMessage = 'There was an error while retrieving employee information.';
    }    
}

$pageTitle = 'Employee';
include ROOT_PATH . '/public/header.php';
include ROOT_PATH . '/public/nav.php';
include ROOT_PATH . '/public/nav_back.php';

?>

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
                <p><strong>Department: </strong><a href="<?=BASE_URL . '/views/department/view.php?id=' . $employee['department_id'] ?>"><?=htmlspecialchars($employee['department_name']) ?></a></p>
                <section class="employees">
                    <header>
                        <h2>Projects</h2>
                    </header>
                    <?php if ($projects): ?>
                        <ul>
                            <?php foreach ($projects as $project): ?>
                                <li>
                                    <a href="<?=BASE_URL . 
                                        '/views/project/view.php?id=' . 
                                        $project['project_id'] ?>"><?=$project['name'] ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>This employee is not assigned to any project.</p>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </section>
    </main>

<?php include ROOT_PATH . '/public/footer.php'; ?>