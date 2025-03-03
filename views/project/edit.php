<?php

$errorMessage = '';

require_once '../../initialise.php';

require_once ROOT_PATH . '/classes/Employee.php';
$employee = new Employee();

require_once ROOT_PATH . '/classes/Project.php';
$project = new Project();

if ($employee->connexionError || $project->connexionError) {
    $errorMessage = 'There was an error while connecting to the database.';
} else {
    $projectID = (int) ($_GET['id'] ?? 0);
    
    if ($projectID === 0) {
        header('Location: index.php');
        exit;
    }

    // The list of employees will be displayed in the dropdown to add employees
    $employeeList = $employee->getAll();
    if (!$employeeList) {
        $errorMessage = 'There was an error while retrieving the list of employees.';
    } else {
        
        // The project's name and list of assigned employees is retrieved for display
        $projectToUpdate = $project->getByID($projectID);
        if (!$projectToUpdate) {
            $errorMessage = 'There was an error while retrieving project information.';
        } else {

            $name = $projectToUpdate[0]['project_name'];

            // Employees already assigned to the project are removed from the list of employees to add
            $assignedEmployees = array_column($projectToUpdate, 'employee_id');
            $employeeList = array_filter($employeeList, function ($employee) use ($assignedEmployees) {
                return !in_array($employee['nEmployeeID'], $assignedEmployees);
            });
            $employeeList = array_values($employeeList);

            /**
             * As there are 3 forms, there can be 3 types of POST requests:
             * 1. Edit the name of the project ('update_project')
             * 2. Add an employee to the project ('add_employee')
             * 3. Remove an employee from the project ('remove_employee')
             */            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['update_project'])) {
                    $validationErrors = $project->validate($_POST);
                    
                    if (!$validationErrors) {
                        if (!$project->update($projectID, $_POST)) {
                            $errorMessage = 'It was not possible to edit the project.';
                        } else {
                            header('Location: index.php');
                        }
                    }
                } elseif (isset($_POST['add_employee'])) {
                    $employeeID = (int) ($_POST['employee'] ?? 0);

                    if ($employeeID === 0) {
                        $errorMessage = 'Invalid employee.';
                    } else {
                        if (!$project->addEmployee($projectID, $employeeID)) {
                            $errorMessage = 'It was not possible to add the employee to the project.';
                        } else {
                            header('Location: index.php');
                        }
                    }                
                } elseif (isset($_POST['remove_employee'])) {
                    $employeeID = (int) ($_POST['remove_employee'] ?? 0);
                    
                    if ($employeeID === 0) {
                        $errorMessage = 'Invalid employee.';
                    } else {
                        if (!$project->removeEmployee($projectID, $employeeID)) {
                            $errorMessage = 'It was not possible to remove the employee from the project.';
                        } else {
                            header('Location: index.php');
                        }
                    }
                }
            }
        }
    }
}

$pageTitle = 'Edit Project';
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
            <form action="edit.php?id=<?=$projectID ?>" method="POST">
                <div>
                    <label for="txtName">Name</label>
                    <input type="text" id="txtName" name="name"
                        value="<?=$name ?? '' ?>">
                </div>
                <div>
                    <button type="submit" name="update_project">Edit project name</button>
                </div>
            </form>
            <section class="employees">
                <header>
                    <h2><strong>Employees:</strong></h2>
                </header>
                <form action="edit.php?id=<?=$projectID ?>" method="POST">
                    <ul>
                        <?php foreach ($projectToUpdate as $employee): ?>
                            <?php if ($employee['first_name'] === null): ?>
                                <p>No employees assigned to this project yet.</p>
                            <?php else: ?>
                                <li>
                                    <button type="submit" name="remove_employee" 
                                        value="<?=$employee['employee_id'] ?>">Remove</button>
                                    <?=$employee['last_name'] . ', ' . 
                                        $employee['first_name'] . ' (' . 
                                        $employee['department_name'] . ')' ?>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </form>                
            </section>
            <section class="employees">
                <header>
                    <h2>Add employees</h2>
                </header>
                <form action="edit.php?id=<?=$projectID ?>" method="POST">
                    <div>
                        <label for="cmbEmployees">Employees:</label>
                        <select name="employee" id="cmbEmployees">
                            <?php foreach ($employeeList as $employee): ?>
                                <option 
                                    value="<?=$employee['nEmployeeID'] ?>"><?=$employee['cLastName'] . 
                                    ', ' . $employee['cFirstName'] . ' (' . $employee['cName']. ')' ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <button type="submit" name="add_employee">Add</button>
                    </div>
                </form>
            </section>
        <?php endif; ?>
    </main>
<?php include ROOT_PATH . '/public/footer.php'; ?>