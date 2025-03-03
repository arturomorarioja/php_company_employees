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
    
    $departmentToUpdate = $department->getByID($departmentID)[0];
    if (!$departmentToUpdate) {
        $errorMessage = 'There was an error while retrieving department information';
    } else {
        $name = $departmentToUpdate['department_name'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {               
            $validationErrors = $department->validate($_POST);
            
            if (!$validationErrors) {
                if (!$department->update($departmentID, $_POST)) {
                    $errorMessage = 'It was not possible to edit the department.';
                } else {
                    header('Location: index.php');
                }
            }
        }
    }
}

$pageTitle = 'Edit department';
include ROOT_PATH . '/public/header.php';
include ROOT_PATH . '/public/nav.php';
include ROOT_PATH . '/public/nav_back.php';

?>
    <main>
        <?php if ($errorMessage): ?>
            <p class="error"><?=$errorMessage ?></p>
        <?php else: ?>
            <?php if (isset($validationErrors) && !empty($validationErrors)): ?>
                <section class="error">
                    <?php foreach ($validationErrors as $validationError): ?>
                        <p><?=$validationError ?></p>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>
            <form action="edit.php?id=<?=$departmentID ?>" method="POST">
                <div>
                    <label for="txtName">Name</label>
                    <input type="text" id="txtName" name="name"
                        value="<?=$name ?? '' ?>">
                </div>
                <div>
                    <button type="submit">Edit department</button>
                </div>
            </form>
        <?php endif; ?>
    </main>
<?php include ROOT_PATH . '/public/footer.php'; ?>