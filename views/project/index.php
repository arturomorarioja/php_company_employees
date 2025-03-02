<?php

require_once '../../initialise.php';
require_once ROOT_PATH . '/classes/Project.php';

$errorMessage = '';

$project = new Project();
if ($project->connexionError) {
    $errorMessage = 'There was an error while connecting to the database.';
} else {
    $searchText = trim($_GET['search'] ?? '');

    if ($searchText === '') {
        $projects = $project->getAll();
    } else {
        $projects = $project->search($searchText);
    }
    if (!$projects) {
        $errorMessage = 'There was an error while retrieving the list of projects';
    }
}

$pageTitle = 'Projects';
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
                <li><a href="new.php">Add project</a></li>
            </ul>
        </nav>
        <section>
            <?php if ($errorMessage): ?>
                <p><?=$errorMessage ?></p>
            <?php else: ?>
                <?php foreach ($projects as $project): ?>
                    <article data-id="<?=$project['nProjectID'] ?>">
                        <section>
                            <p><strong>Name: </strong><?=$project['cName'] ?></p>
                        </section>
                        <nav>
                            <ul>
                                <li><a href="view.php?id=<?=$project['nProjectID'] ?>">Show details</a></li>
                                <li><a href="edit.php?id=<?=$project['nProjectID'] ?>">Edit project</a></li>
                                <li><a href="delete.php?id=<?=$project['nProjectID'] ?>">Delete project</a></li>
                            </ul>
                        </nav>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
<?php include '../../public/footer.php'; ?>