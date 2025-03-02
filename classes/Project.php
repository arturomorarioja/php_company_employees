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
     * It retrieves employees from the database based 
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
     * @return An associative array with employee information,
     *         or false if there was an error
     */
    public function getByID(int $projectID): array|false
    {
        $sql =<<<SQL
            SELECT 
                project.cName AS project_name,
                employee.cFirstName AS first_name, 
                employee.cLastName AS last_name, 
                employee.cEmail AS email, 
                employee.dBirth AS birth_date, 
                employee.nDepartmentID AS department_id, 
                department.cName AS department_name
            FROM project
                LEFT JOIN emp_proy
                    ON project.nProjectID = emp_proy.nProjectID
                INNER JOIN employee
                    ON emp_proy.nEmployeeID = employee.nEmployeeID 
                INNER JOIN department
                    ON employee.nDepartmentID = department.nDepartmentID                
            WHERE emp_proy.nProjectID = :projectID;
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
     * @param $employee Project data in an associative array
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
            $stmt->bindValue(':firstName', $project['name']);
            $stmt->execute();
            
            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            Logger::logText('Error inserting a new project: ', $e);
            return false;
        }
    }

    /**
     * Updates a project in the database
     * @param $this->pdo A PDO database connection
     * @param $projectID The project's ID
     * @param $project An associative array with project information
     * @return true if the edition was successful,
     *         or false if there was an error
     */
    public function update(int $projectID, array $project): bool
    {
        $sql =<<<SQL
            UPDATE project
            SET cName = :name
            WHERE nProjectID = :projectID;
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $project['name']);
            $stmt->bindValue(':projectID', $projectID);
            $stmt->execute();
            
            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            Logger::logText('Error updating a project: ', $e);
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
            DELETE FROM project
            WHERE nProjectID = :projectID;
        SQL;
        try {
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':projectID', $projectID);
            $stmt->execute();
            
            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            Logger::logText('Error deleting a project: ', $e);
            return false;
        }
    }
}