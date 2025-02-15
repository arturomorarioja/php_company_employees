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
        <nav>
            <ul>
                <li><a href="new.php">Add employee</a></li>
            </ul>
        </nav>
        <section>
            <?php if ($errorMessage): ?>
                <p><?=$errorMessage ?></p>
            <?php else: ?>
                <?php foreach ($employees as $employee): ?>
                    <article data-id="<?=$employee['nEmployeeID'] ?>">
                        <section>
                            <p><strong>First name: </strong><?=$employee['cFirstName'] ?></p>
                            <p><strong>Last name: </strong><?=$employee['cLastName'] ?></p>
                            <p><strong>Birth date: </strong><?=$employee['dBirth'] ?></p>
                        </section>
                        <nav>
                            <ul>
                                <li><a href="view.php?id=<?=$employee['nEmployeeID'] ?>">Show details</a></li>
                            </ul>
                        </nav>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
<?php include 'public/footer.php'; ?>