<?php

require_once 'src/database.php';
require_once 'src/employees.php';

$errorMessage = '';

$pdo = connect();
if (!$pdo) {
    $errorMessage = 'There was an error while connecting to the database.';
} else {
    $searchText = trim($_GET['search'] ?? '');

    if ($searchText === '') {
        $employees = getAllEmployees($pdo);
    } else {
        $employees = searchEmployees($pdo, $searchText);
    }
    if (!$employees) {
        $errorMessage = 'There was an error while retrieving the list of employees';
    }
}

$pageTitle = 'Employees';
include 'public/header.php';

?>
    <main>
        <form action="index.php" method="GET">
            <div id="searchForm">
                <label for="txtSearch"></label>
                <input type="search" id="txtSearch" name="search">
                <button type="submit">Search</button>
            </div>            
        </form>
        <form action="new.php" method="GET">              
            <button type="submit">Add employee</button>
        </form>
        <section>
            <?php if ($errorMessage): ?>
                <p><?=$errorMessage ?></p>
            <?php else: ?>
                <?php foreach ($employees as $employee): ?>
                    <article>
                        <p><strong>First name: </strong><?=$employee['cFirstName'] ?></p>
                        <p><strong>Last name: </strong><?=$employee['cLastName'] ?></p>
                        <p><strong>Birth date: </strong><?=$employee['dBirth'] ?></p>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
<?php include 'public/footer.php'; ?>