<?php

/**
 * Gathers all departments from the database
 * @param $pdo A PDO database connection
 * @return An array with all the departments,
 *         or false if there was an error
 */
function getAllDepartments (PDO $pdo): array|false
{
    $sql =<<<SQL
        SELECT nDepartmentID, cName
        FROM department
        ORDER BY cName;
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
 * Gathers a department from the database based on its ID
 * @param $pdo A PDO database connection
 * @param $departmentID The department ID to find
 * @return The name of the department,
 *         or false if there was an error
 */
function getDepartmentByID (PDO $pdo, int $departmentID): string|false
{
    $sql =<<<SQL
        SELECT cName
        FROM department
        WHERE nDepartmentID = :departmentID;
    SQL;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':departmentID', $departmentID);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            return $stmt->fetch()['cName'];
        } else {
            return false;
        }
    } catch (Exception) {
        return false;
    }
}