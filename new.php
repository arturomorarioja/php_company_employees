<?php

$pageTitle = 'Add Employee';
include 'public/header.php';

require_once 'classes/Department.php';
$department = new Department();

if ($department->connexionError) {
    $errorMessage = 'There was an error while connecting to the database.';
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once 'classes/Employee.php';
        
        $employee = new Employee();
        if ($employee->connexionError) {
            $errorMessage = 'There was an error while connecting to the database.';
        } else {
            $validationErrors = $employee->validate($_POST);    
            if (!$validationErrors) {
                if (!$employee->insert($_POST)) {
                    $errorMessage = 'It was not possible to add the new employee.';
                } else {
                    header('Location: index.php');
                }
            }
        }
    }
}

?>
    <main>
        <nav class="nav">
            <ul>
                <li><a href="index.php" title="Homepage">Back</a></li>
            </ul>
        </nav>
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
                    <label for="txtFirstName">First name</label>
                    <input type="text" id="txtFirstName" name="first_name"
                        value="<?=$_POST['first_name'] ?? '' ?>">
                </div>
                <div>
                    <label for="txtLastName">Last name</label>
                    <input type="text" id="txtLastName" name="last_name"
                        value="<?=$_POST['last_name'] ?? '' ?>">
                </div>
                <div>
                    <label for="txtEmail">Email</label>
                    <input type="email" id="txtEmail" name="email"
                        value="<?=$_POST['email'] ?? '' ?>">
                </div>
                <div>
                    <label for="txtBirthDate">Birth date</label>
                    <input type="date" id="txtBirthDate" name="birth_date"
                        value="<?=$_POST['birth_date'] ?? null ?>">
                </div>
                <div>
                    <?php
                        require_once 'classes/department.php';

                        $departments = $department->getAll();
                        if (!$departments):
                    ?>
                            <p class="error">It was not possible to display the list of departments.</p>
                    <?php else: ?>
                            <label for="cmbDepartment">Department</label>
                            <select id="cmbDepartment" name="department">
                                <?php foreach ($departments as $department): ?>
                                    <option 
                                        value="<?=$department['nDepartmentID'] ?>" 
                                        <?=($department['nDepartmentID'] == ($_POST['department'] ?? 0) ? 'selected': '') ?>>
                                            <?=$department['cName'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                    <?php endif; ?>
                </div>
                <div>
                    <button type="submit">Add employee</button>
                </div>
            </form>
        <?php endif; ?>
    </main>
<?php include 'public/footer.php'; ?>