<?php

/**
 * Gathers all employees from the database
 * @param $pdo A PDO database connection
 * @return An array with all the employees,
 *         or false if there was an error
 */
function getAllEmployees(PDO $pdo): array|false
{
    $sql =<<<SQL
        SELECT nEmployeeID, cFirstName, cLastName, dBirth
        FROM employee
        ORDER BY cFirstName, cLastName;
    SQL;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (Exception) {
        return false;
    }
}

/**
 * Retrieves an employee's information from the database
 * @param $pdo A PDO database connection
 * @param $employeeID The ID of the employee to retrieve
 * @return An array with employee information,
 *         or false if there was an error
 */
function getEmployeeByID(PDO $pdo, int $employeeID): array|false
{
    $sql =<<<SQL
        SELECT 
            employee.cFirstName, employee.cLastName, employee.cEmail, 
            employee.dBirth, employee.nDepartmentID, department.cName
        FROM employee INNER JOIN department
            ON employee.nDepartmentID = department.nDepartmentID
        WHERE employee.nEmployeeID = :employeeID;
    SQL;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':employeeID', $employeeID);
        $stmt->execute();

        return $stmt->fetch();
    } catch (Exception) {
        return false;
    }
}

/**
 * Searches employees from the database
 * @param $pdo A PDO database connection
 * @param $searchText The text to search for
 * @return An array with the employees whose first or last name satisfies the search text,
 *         or false if there was an error
 */
function searchEmployees(PDO $pdo, string $searchText): array|false 
{
    $sql =<<<SQL
        SELECT cFirstName, cLastName, dBirth
        FROM employee
        WHERE cFirstName LIKE :firstName
           OR cLastName LIKE :lastName
        ORDER BY cFirstName, cLastName;
    SQL;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':firstName', "%$searchText%");
        $stmt->bindValue(':lastName', "%$searchText%");
        $stmt->execute();

        return $stmt->fetchAll();
    } catch (Exception) {
        return false;
    }
}

/**
 * Validates employee information before its submission to the database
 * @param $pdo A PDO database connection
 * @param $employee An array with employee information
 * @return An array with all the validation errors,
 *         or false if the data is correct
 */
function employeeValidationErrors(PDO $pdo, array $employee): array|false
{
    $firstName = trim($employee['first_name'] ?? '');
    $lastName = trim($employee['last_name'] ?? '');
    $email = trim($employee['email'] ?? '');
    $birthDate = $employee['birth_date'] ?? '';
    $departmentID = (float)($employee['department'] ?? 0);

    $validationErrors = [];
    if ($firstName === '') {
        $validationErrors[] = 'First name cannot be empty.';
    }
    if ($lastName === '') {
        $validationErrors[] = 'Last name cannot be empty.';
    }
    if ($email === '') {
        $validationErrors[] = 'Email cannot be empty.';
    } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        $validationErrors[] = 'Incorrect email format.';
    }
    if (in_array($birthDate, [null, ''])) {
        $validationErrors[] = 'Birth date cannot be empty.';
    } elseif (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $birthDate)) {
        $validationErrors[] = 'Incorrect birth date format.';
    } elseif (DateTime::createFromFormat('Y-m-d', $birthDate) > new DateTime('-16 years')) {
        $validationErrors[] = 'The employee must be at least 16 years old.';
    }
    
    require_once 'department.php';
    if ($departmentID === 0) {
        $validationErrors[] = 'Department cannot be empty.';
    } elseif (!getDepartmentByID($pdo, $departmentID)) {
        $validationErrors[] = 'Nonexisting department.';
    }

    if (empty($validationErrors)) {
        return false;
    }
    return $validationErrors;
}

/**
 * Inserts an employee in the database
 * @param $pdo A PDO database connection
 * @param $firstName The employee's first name
 * @param $lastName The employee's last name
 * @param $email The employee's email address
 * @param $birthDate The employee's birth date
 * @param $departmentID The employee's department ID
 * @return true if the insertion was successful,
 *         or false if there was an error
 */
function insertEmployee (
    PDO $pdo,
    string $firstName, 
    string $lastName, 
    string $email, 
    string $birthDate,
    int $departmentID
): bool
{
echo '<pre>';
    $sql =<<<SQL
        INSERT INTO employee
            (cFirstName, cLastName, cEmail, dBirth, nDepartmentID)
        VALUES
            (:firstName, :lastName, :email, :birthDate, :departmentID);
    SQL;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':firstName', $firstName);
        $stmt->bindValue(':lastName', $lastName);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':birthDate', $birthDate);
        $stmt->bindValue(':departmentID', $departmentID);
        $stmt->execute();

        return $stmt->rowCount() === 1;
    } catch (Exception) {
        return false;
    }
}

/**
 * Deletes an employee in the database
 * @param $pdo A PDO database connection
 * @param $employeeID The ID of the employee to delete
 * @return true if the deletion was successful,
 *         or false if there was an error
 */
function deleteEmployee(PDO $pdo, int $employeeID): bool
{
    $sql =<<<SQL
        DELETE FROM employee
        WHERE nEmployeeID = :employeeID;
    SQL;
    try {

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':employeeID', $employeeID);
        $stmt->execute();

        return $stmt->rowCount() === 1;
    } catch (Exception) {
        return false;
    }
}