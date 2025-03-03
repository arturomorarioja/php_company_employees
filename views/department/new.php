<?php

require_once '../../initialise.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once ROOT_PATH . '/classes/Department.php';
    
    $department = new Department();
    if ($department->connexionError) {
        $errorMessage = 'There was an error while connecting to the database.';
    } else {
        $validationErrors = $department->validate($_POST);    
        if (!$validationErrors) {
            if (!$department->insert($_POST)) {
                $errorMessage = 'It was not possible to add the new department.';
            } else {
                header('Location: index.php');
            }
        }
    }
}

$pageTitle = 'Add Department';
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
                    <button type="submit">Add department</button>
                </div>
            </form>
        <?php endif; ?>
    </main>
<?php include ROOT_PATH . '/public/footer.php'; ?>