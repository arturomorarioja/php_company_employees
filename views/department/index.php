<?php

require_once '../../initialise.php';
require_once ROOT_PATH . '/classes/Department.php';

$errorMessage = '';

$department = new Department();
if ($department->connexionError) {
    $errorMessage = 'There was an error while connecting to the database.';
} else {
    $searchText = trim($_GET['search'] ?? '');

    if ($searchText === '') {
        $departments = $department->getAll();
    } else {
        $departments = $department->search($searchText);
    }
    if (!$departments) {
        $errorMessage = 'There was an error while retrieving the list of departments';
    }
}

$pageTitle = 'Departments';
include ROOT_PATH . '/public/header.php';
include ROOT_PATH . '/public/nav.php';

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
                <li><a href="new.php">Add department</a></li>
            </ul>
        </nav>
        <section>
            <?php if ($errorMessage): ?>
                <p><?=$errorMessage ?></p>
            <?php else: ?>
                <?php foreach ($departments as $department): ?>
                    <article data-id="<?=$department['nDepartmentID'] ?>">
                        <section>
                            <p><strong>Name: </strong><?=$department['cName'] ?></p>
                        </section>
                        <nav>
                            <ul>
                                <li><a href="view.php?id=<?=$department['nDepartmentID'] ?>">Show details</a></li>
                                <li><a href="edit.php?id=<?=$department['nDepartmentID'] ?>">Edit department</a></li>
                                <li><a href="delete.php?id=<?=$department['nDepartmentID'] ?>">Delete department</a></li>
                            </ul>
                        </nav>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
<?php include '../../public/footer.php'; ?>