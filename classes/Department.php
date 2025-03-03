<?php

require_once 'Database.php';
require_once 'Logger.php';

Class Department extends Database 
{  
    /**
     * It retrieves all department from the database
     * @return An associative array with department information,
     *         or false if there was an error
     */
    public function getAll(): array|false
    {
        $sql =<<<SQL
            SELECT nDepartmentID, cName
            FROM department
            ORDER BY cName
        SQL;
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::logText('Error retrieving all departments: ', $e);
            return false;
        }
    }

    /**
     * It retrieves departments from the database based on a name search
     * @param $searchText The text to search in the database
     * @return An associative array with department information,
     *         or false if there was an error
     */
    public function search(string $searchText): array|false
    {
        $sql =<<<SQL
            SELECT nDepartmentID, cName
            FROM department
            WHERE cName LIKE :name
            ORDER BY cName
        SQL;
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', "%$searchText%");
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::logText('Error retrieving all departments: ', $e);
            return false;
        }
    }

    /**
     * It retrieves information regarding a department, including its list of employees
     * @param $departmentID The ID of the department whose info to retrieve
     * @return An associative array with department information,
     *         or false if there was an error
     */
    public function getByID(int $departmentID): array|false
    {
        $sql =<<<SQL
            SELECT 
                department.cName AS department_name,
                employee.nEmployeeID AS employee_id,
                employee.cFirstName AS first_name,
                employee.cLastName AS last_name                
            FROM department LEFT JOIN employee
                ON department.nDepartmentID = employee.nDepartmentID                
            WHERE department.nDepartmentID = :departmentID;
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':departmentID', $departmentID);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::logText('Error retrieving all departments: ', $e);
            return false;
        }
    }
    /**
     * It validates department data before saving it to the database
     * @param $department Department data in an associative array
     * @return<array> An array with all validation error messages
     */
    public function validate(array $department): array
    {
        $name = trim($department['name'] ?? '');

        $validationErrors = [];

        if ($name === '') {
            $validationErrors[] = 'Name is mandatory.';            
        }

        return $validationErrors;
    }

    /**
     * It inserts a new department in the database
     * @param $department An associative array with department information
     * @return true if the insert was successful,
     *         or false if there was an error
     */
    public function insert(array $department): bool
    {
        $sql =<<<SQL
            INSERT INTO department
                (cName)
            VALUES
                (:name);
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $department['name']);
            $stmt->execute();
            
            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            Logger::logText('Error inserting a new department: ', $e);
            return false;
        }
    }

    /**
     * Updates a department in the database
     * @param $departmentID The department's ID
     * @param $department An associative array with the name of the department
     * @return true if the edition was successful,
     *         or false if there was an error
     */
    public function update(int $departmentID, array $department): bool
    {
        try {
            $this->pdo->beginTransaction();

            /**
             * The department name is only updated if it has changed
             */
            $sql =<<<SQL
                SELECT cName
                FROM department
                WHERE nDepartmentID = :departmentID;
            SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':departmentID', $departmentID);
            $stmt->execute();

            if ($stmt->rowCount() !== 1) {
                $this->pdo->rollBack();

                Logger::logText('Error updating department name.');
                return false;
            }

            if ($stmt->fetch()['cName'] !== $department['name']) {
                $sql =<<<SQL
                    UPDATE department
                    SET cName = :name
                    WHERE nDepartmentID = :departmentID;
                SQL;
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':name', $department['name']);
                $stmt->bindValue(':departmentID', $departmentID);
                $stmt->execute();

                if ($stmt->rowCount() !== 1) {
                    $this->pdo->rollBack();
    
                    Logger::logText('Error updating department name.');
                    return false;
                }
            }
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();

            Logger::logText('Error updating a department: ', $e);
            return false;
        }
    }

    /**
     * Deletes a department in the database
     * @param $departmentID The ID of the department to delete
     * @return true if the deletion was successful,
     *         false if there was an error,
     *         or a message if the department cannot be deleted because it contains employees
     */
    public function delete(int $departmentID): bool|string
    {        
        $sql =<<<SQL
            SELECT COUNT(*) AS Total
            FROM employee
            WHERE nDepartmentID = :departmentID;
        SQL;
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':departmentID', $departmentID);
            $stmt->execute();

            if ($stmt->fetch()['Total'] > 0) {
                return 'The department is not empty.';
            }
            
            $sql =<<<SQL
                DELETE FROM department
                WHERE ndepartmentID = :departmentID;
            SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':departmentID', $departmentID);
            $stmt->execute();
            $success = $stmt->rowCount() === 1;

            $this->pdo->commit();
            return $success;
        } catch (PDOException $e) {
            $this->pdo->rollBack();

            Logger::logText('Error deleting a department: ', $e);
            return false;
        }
    }
}