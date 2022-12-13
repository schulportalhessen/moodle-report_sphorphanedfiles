<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace report_sphorphanedfiles\Database;

use moodle_database;

defined('MOODLE_INTERNAL') || die();

/**
 * This class provides high-level functionality for the module. Concepts like
 * enumerating files belonging to a specific component are mapped to the
 * relevant SQL queries, therefore encapsulating low-level database access inside
 * this class.
 */
class DataFiles
{
    /**
     * @var moodle_database The database connection an instance of this class
     *                      operates on.
     */
    private $dbM;

    /**
     * Creates a new instance which is bound to a database using the given
     * database connection.
     *
     * @param moodle_database $dbM The database connection to be used by this
     *                             instance.
     */
    public function __construct(moodle_database $dbM) {
        $this->dbM = $dbM;
    }

    /**
     * Returns the database connection used by the instance.
     *
     * @return moodle_database The database instance.
     */
    public function getDatabase(): moodle_database {
        return $this->dbM;
    }

    /**
     * Queries created by this class are based on SELECT statements. The Moodle
     * database subsystem provides functionality for statement construction, i.e.
     * a mechanism that substitutes variables in strings with concrete values.
     *
     * This method creates strings that follow this pattern. For each variable
     * name in the parameter array, a corresponding entry in the result array is
     * created, consisting of the variable's name (SQL world) and its substitution
     * position (Moodle world), i.e. the name prefixed with „:“.
     *
     * Example: „userid“ ---> „userid = :userid“
     *
     * @param array $elements The array of strings which should be interpreted as
     *                        variable names.
     *
     * @return array An array of strings conforming to the described structural
     *               pattern.
     */
    protected function createWhereString(array $elements): array {
        return array_map(function ($element) {
            return $element . " = :" . $element;
        }, $elements);
    }

    /**
     * Prepares the statement to be emitted to the database layer of the Moodle
     * system. Given parameters are combined using AND, forming the final
     * WHERE clause.
     *
     * @param array $params An array whose keys should be used as components of the
     *                      WHERE clause for a SELECT statement.
     *
     * @return string A valid SQL statement ready to be used with the Moodle database
     *                subsystem.
     */
    protected function prepareStatement(array $params): string {
        $where = implode(" AND ", $this->createWhereString(array_keys($params)));

        return "SELECT * FROM {files} WHERE {$where}";
    }

    /**
     * Performs a query using the keys and values of the parameter array as part
     * of the command's WHERE clause.
     *
     * @param array $params An array whose keys should be used as components of the
     *                      WHERE clause for the SELECT statement.
     *
     * @return array An array containing the results of the performed query.
     */
    protected function performQuery(array $params): array {
        $sql = $this->prepareStatement($params);
        return $this->getDatabase()->get_records_sql($sql, $params);
    }

    /**
     * Provides a dictionary with preset keys having the given values.
     *
     * @return array The dictionary containing the given information at the right places.
     *
     */
    protected function prepareContextParameters($contextId, $modName): array {
        return ['component' => sprintf('mod_%s', $modName), 'contextid' => $contextId];
    }


    /**
     * Retrieves the files associated with a component.
     *
     * @param int $contextId The context, i.e. the „instance-number“, of the component.
     * @param string $modName The component's name.
     *
     * @return array An array listing all files for the component's intro.
     *
     * @throws \dml_exception
     */
    public function getFilesForComponent(int $contextId, string $modName): array {
        return $this->performQuery($this->prepareContextParameters($contextId, $modName));
    }

    /**
     * Retrieves the files associated with the intro of a component.
     *
     * @param int $contextId The context, i.e. the „instance-number“, of the component.
     * @param string $modName The component's name.
     *
     * @return array An array listing all files for the component's intro.
     *
     * @throws \dml_exception
     */
    public function getFilesForComponentIntro(int $contextId, string $modName): array {
        $params = $this->prepareContextParameters($contextId, $modName);
        $params['filearea'] = 'intro';

        return $this->performQuery($params);
    }

    /**
     * Provides a dictionary with preset keys having the given values.
     *
     * @return array The dictionary containing the given information at the right places.
     *
     */
    protected function prepareSectionParameters($itemId, $courseContextId): array {
        return ['itemid' => $itemId, 'component' => 'course', 'filearea' => 'section', 'contextid' => $courseContextId];
    }

    /**
     * Retrieves the files associated with the summary of a section.
     *
     * @param int $itemId The id of the item.
     * @param int $courseContextId The context, i.e. the „instance-number“, of the section.
     *
     * @return array An array listing all files for the summary of a section.
     *
     * @throws \dml_exception
     */
    public function getFilesForSectionSummary(int $itemId, int $courseContextId): array {
        return $this->performQuery($this->prepareSectionParameters($itemId, $courseContextId));
    }


    public function getCourse($courseId) {
        return $this->getDatabase()->get_record('course', ['id' => $courseId], '*', MUST_EXIST);
    }

    public function getPage($instance) {
        return $this->getDatabase()->get_record('page', ['id' => $instance->instance], '*');
    }
}
