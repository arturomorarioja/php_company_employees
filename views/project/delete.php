<?php

$errorMessage = '';

require_once '../../initialise.php';
require_once ROOT_PATH . '/classes/Project.php';
$project = new Project();
if ($project->connexionError) {
    $errorMessage = 'There was an error while connecting to the database.';
} else {
    $projectID = (int) ($_GET['id'] ?? 0);

    if ($projectID === 0) {
        header('Location: index.php');
        exit;
    }

    $projectToDelete = $project->getByID($projectID);
    if (!$projectToDelete) {
        $errorMessage = 'There was an error while retrieving project information';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $project = $project->delete($projectID);
        if (!$project) {
            $errorMessage = 'There was an error while deleting the project';
        } else {
            header('Location: index.php');
        }
    }
}

$pageTitle = 'Delete project';
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
                    <p>Are you sure that you want to delete the following project?</p>
                </section>
                <p><strong>Name: </strong><?=htmlspecialchars($projectToDelete[0]['project_name']) ?></p>
                <section id="employees">
                    <header>
                        <h2><strong>Employees:</strong></h2>
                    </header>
                    <ul>
                        <?php foreach ($projectToDelete as $employee): ?>
                            <?php if ($employee['first_name'] === null): ?>
                                <p>No employees assigned to this project yet.</p>
                            <?php else: ?>
                                <li><?=$employee['last_name'] . ', ' . $employee['first_name'] . ' (' . $employee['department_name'] . ')' ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>
        </section>
        <form action="delete.php?id=<?=$projectID ?>" method="POST">
            <button type="submit">Delete project</button>
        </form>
    </main>

<?php include ROOT_PATH . '/public/footer.php'; ?>