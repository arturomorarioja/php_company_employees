<?php

require_once 'Database.php';
require_once 'Logger.php';

Class Project extends Database
{
    /**
     * It retrieves all projects from the database
     * @return An associative array with project information,
     *         or false if there was an error
     */
    public function getAll(): array|false
    {
        $sql =<<<SQL
            SELECT nProjectID, cName
            FROM project
            ORDER BY cName;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::LogText('Error retrieving all projects: ', $e);
            return false;
        }
    }

    /**
     * It retrieves projects from the database based 
     * on a name text search
     * @param $searchText The text to search in the database
     * @return An associative array with project information,
     *         or false if there was an error
     */
    public function search(string $searchText): array|false
    {
        $sql =<<<SQL
            SELECT nProjectID, cName
            FROM project
            WHERE cName LIKE :name
            ORDER BY cName;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', "%$searchText%");
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::LogText('Error searching for projects: ', $e);
            return false;
        }
    }

    /**
     * It retrieves a project's names and employees
     * @param $projectID The ID of the project
     * @return<array> An associative array with employee information,
     *         or false if there was an error
     */
    public function getByID(int $projectID): array|false
    {
        $sql =<<<SQL
            SELECT 
                project.cName AS project_name,
                employee.nEmployeeID AS employee_id, 
                employee.cFirstName AS first_name, 
                employee.cLastName AS last_name, 
                employee.cEmail AS email, 
                employee.dBirth AS birth_date, 
                employee.nDepartmentID AS department_id, 
                department.cName AS department_name
            FROM project
                LEFT JOIN emp_proy
                    ON project.nProjectID = emp_proy.nProjectID
                LEFT JOIN employee
                    ON emp_proy.nEmployeeID = employee.nEmployeeID 
                LEFT JOIN department
                    ON employee.nDepartmentID = department.nDepartmentID                
            WHERE project.nProjectID = :projectID;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':projectID', $projectID);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::logText('Error retrieving project information: ', $e);
            return false;
        }
    }

    /**
     * It validates project data before saving it to the database
     * @param $project Project data in an associative array
     * @return<array> An array with all validation error messages
     */
    public function validate(array $project): array
    {
        $name = trim($project['name'] ?? '');

        $validationErrors = [];

        if ($name === '') {
            $validationErrors[] = 'Name is mandatory.';            
        }

        return $validationErrors;
    }

    /**
     * It inserts a new project in the database
     * @param $project An associative array with project information
     * @return true if the insert was successful,
     *         or false if there was an error
     */
    public function insert(array $project): bool
    {
        $sql =<<<SQL
            INSERT INTO project
                (cName)
            VALUES
                (:name);
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $project['name']);
            $stmt->execute();
            
            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            Logger::logText('Error inserting a new project: ', $e);
            return false;
        }
    }

    /**
     * Updates a project in the database
     * @param $projectID The project's ID
     * @param $project An associative array with the name of the project
     * @return true if the edition was successful,
     *         or false if there was an error
     */
    public function update(int $projectID, array $project): bool
    {
        try {
            $this->pdo->beginTransaction();

            /**
             * The project name is only updated if it has changed
             */
            $sql =<<<SQL
                SELECT cName
                FROM project
                WHERE nProjectID = :projectID;
            SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':projectID', $projectID);
            $stmt->execute();

            if ($stmt->rowCount() !== 1) {
                $this->pdo->rollBack();

                Logger::logText('Error updating project name.');
                return false;
            }

            if ($stmt->fetch()['cName'] !== $project['name']) {
                $sql =<<<SQL
                    UPDATE project
                    SET cName = :name
                    WHERE nProjectID = :projectID;
                SQL;
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':name', $project['name']);
                $stmt->bindValue(':projectID', $projectID);
                $stmt->execute();

                if ($stmt->rowCount() !== 1) {
                    $this->pdo->rollBack();
    
                    Logger::logText('Error updating project name.');
                    return false;
                }
            }
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();

            Logger::logText('Error updating a project: ', $e);
            return false;
        }
    }

    /**
     * Adds an employee to a project
     * @param $projectID The ID of the project to add an employee to
     * @param $employeeID The ID of the employee to add to the project
     * @return true if the operation was successful,
     *         false otherwise
     */
    public function addEmployee(int $projectID, int $employeeID): bool
    {
        $sql =<<<SQL
            INSERT INTO emp_proy
                (nProjectID, nEmployeeID)
            VALUES
                (:projectID, :employeeID);
        SQL;        

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':projectID', $projectID);
            $stmt->bindValue(':employeeID', $employeeID);
            $stmt->execute();

            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            Logger::logText('Error adding an employee to a project: ', $e);
            return false;
        }
    }

    /**
     * Removes an employee from a project
     * @param $projectID The ID of the project to remove an employee from
     * @param $employeeID The ID of the employee to remove from the project
     * @return true if the operation was successful,
     *         false otherwise
     */
    public function removeEmployee(int $projectID, int $employeeID): bool
    {
        $sql =<<<SQL
            DELETE FROM emp_proy
            WHERE nProjectID = :projectID
              AND nEmployeeID = :employeeID;
        SQL;        

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':projectID', $projectID);
            $stmt->bindValue(':employeeID', $employeeID);
            $stmt->execute();

            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            Logger::logText('Error removing an employee from a project: ', $e);
            return false;
        }
    }

    /**
     * Deletes a project in the database
     * @param $projectID The ID of the project to delete
     * @return true if the deletion was successful,
     *         or false if there was an error
     */
    public function delete(int $projectID): bool
    {        
        $sql =<<<SQL
            DELETE FROM emp_proy
            WHERE nProjectID = :projectID;
        SQL;
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':projectID', $projectID);
            $stmt->execute();                     
            
            $sql =<<<SQL
                DELETE FROM project
                WHERE nProjectID = :projectID;
            SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':projectID', $projectID);
            $stmt->execute();
            $success = $stmt->rowCount() === 1;

            $this->pdo->commit();
            return $success;
        } catch (PDOException $e) {
            $this->pdo->rollBack();

            Logger::logText('Error deleting a project: ', $e);
            return false;
        }
    }
}