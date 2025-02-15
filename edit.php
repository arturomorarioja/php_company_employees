<?php

require_once 'src/database.php';
require_once 'src/employees.php';

$errorMessage = '';

$pdo = connect();
if (!$pdo) {
    $errorMessage = 'There was an error while connecting to the database.';
} else {
    $employeeID = (int) ($_GET['id'] ?? 0);

    if ($employeeID === 0) {
        header('Location: index.php');
        exit;
    }

    $employee = getEmployeeByID($pdo, $employeeID);
    if (!$employee) {
        $errorMessage = 'There was an error while retrieving employee information';
    } else {
        $firstName = $employee['cFirstName'];
        $lastName = $employee['cLastName'];
        $email = $employee['cEmail'];
        $birthDate = $employee['dBirth'];
        $departmentID = $employee['nDepartmentID'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once 'src/database.php';
            require_once 'src/employees.php';
            
            $validationErrors = employeeValidationErrors($pdo, $_POST);
        
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $birthDate = $_POST['birth_date'] ?? '';
            $departmentID = (float)($_POST['department'] ?? 0);
        
            if (!$validationErrors) {
                if (!updateEmployee($pdo, $employeeID, $firstName, $lastName, $email, $birthDate, $departmentID)) {
                    $errorMessage = 'It was not possible to add the new employee.';
                } else {
                    header('Location: index.php');
                }
            }
        }
    }
}

$pageTitle = 'Edit Employee';
include 'public/header.php';

?>
    <main>
        <nav class="nav">
            <ul>
                <li><a href="index.php" title="Homepage">Back</a></li>
            </ul>
        </nav>
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
            <form action="edit.php?id=<?=$employeeID ?>" method="POST">
                <div>
                    <label for="txtFirstName">First name</label>
                    <input type="text" id="txtFirstName" name="first_name"
                        value="<?=$firstName ?? '' ?>">
                </div>
                <div>
                    <label for="txtLastName">Last name</label>
                    <input type="text" id="txtLastName" name="last_name"
                        value="<?=$lastName ?? '' ?>">
                </div>
                <div>
                    <label for="txtEmail">Email</label>
                    <input type="email" id="txtEmail" name="email"
                        value="<?=$email ?? '' ?>">
                </div>
                <div>
                    <label for="txtBirthDate">Birth date</label>
                    <input type="date" id="txtBirthDate" name="birth_date"
                        value="<?=$birthDate ?? null ?>">
                </div>
                <div>
                    <?php
                        require_once 'src/database.php';
                        require_once 'src/department.php';

                        if (!isset($pdo)) {
                            $pdo = connect();
                        }
                        $departments = getAllDepartments($pdo);

                        if (!$departments):
                            echo '<p class="error">It was not possible to display the list of departments.</p>';
                        else:
                    ?>
                            <label for="cmbDepartment">Department</label>
                            <select id="cmbDepartment" name="department">
                                <?php foreach ($departments as $department): ?>
                                    <option 
                                        value="<?=$department['nDepartmentID'] ?>" 
                                        <?=($department['nDepartmentID'] == ($departmentID ?? 0) ? 'selected': '') ?>>
                                            <?=$department['cName'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                    <?php endif; ?>
                </div>
                <div>
                    <button type="submit">Edit employee</button>
                </div>
            </form>
        <?php endif; ?>
    </main>
<?php include 'public/footer.php'; ?>