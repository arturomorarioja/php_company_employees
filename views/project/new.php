<?php

require_once '../../initialise.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once ROOT_PATH . '/classes/Project.php';
    
    $project = new Project();
    if ($project->connexionError) {
        $errorMessage = 'There was an error while connecting to the database.';
    } else {
        $validationErrors = $project->validate($_POST);    
        if (!$validationErrors) {
            if (!$project->insert($_POST)) {
                $errorMessage = 'It was not possible to add the new project.';
            } else {
                header('Location: index.php');
            }
        }
    }
}

$pageTitle = 'Add Project';
include ROOT_PATH . '/public/header.php';
include ROOT_PATH . '/public/nav.php';
include ROOT_PATH . '/public/nav_back.php';

?>
    <main>
        <?php if (isset($errorMessage)): ?>
            <p class="error"><?=$errorMessage ?></p>
        <?php else: ?>
            <?php if (isset($validationErrors) && !empty($validationErrors)): ?>
                <section class="error">
                    <?php foreach ($validationErrors as $validationError): ?>
                        <p><?=$validationError ?></p>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>
            <form action="new.php" method="POST">
                <div>
                    <label for="txtName">Name</label>
                    <input type="text" id="txtName" name="name"
                        value="<?=$_POST['name'] ?? '' ?>">
                </div>
                <div>
                    <button type="submit">Add employee</button>
                </div>
            </form>
        <?php endif; ?>
    </main>
<?php include ROOT_PATH . '/public/footer.php'; ?>