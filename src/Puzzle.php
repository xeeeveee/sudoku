<?php

/*
 * TODO: Enhance isSolved() to ensure the solution solves the puzzle stored in $this->puzzle and is not just a valid
 * solution
 */

namespace Xeeeveee\Sudoku;

class Puzzle
{
    /**
     * Holds the puzzle
     *
     * @var array
     */
    protected $puzzle = [];

    /**
     * Holds the solution
     *
     * @var array
     */
    protected $solution = [];

    /**
     * The size of the grid
     *
     * @var int
     */
    protected $CellSize = 3;

    /**
     * Sets the puzzle on construction
     *
     * @param array $puzzle
     * @param array $solution
     */
    public function __construct(array $puzzle = [], array $solution = [])
    {
        $this->setPuzzle($puzzle);
        $this->setSolution($solution);
    }

    /**
     * Gets the grid size
     * 
     * @return int
     */
    public function getCellSize()
    {
        return $this->CellSize;
    }

    /**
     * Sets the grid size
     *
     * @param int $CellSize
     * @return bool
     */
    public function setCellSize($CellSize)
    {
        if(is_integer($CellSize)) {
            $this->CellSize = $CellSize;
            return true;
        }

        return false;
    }

    /**
     * Returns the puzzle array
     * @return array
     */
    public function getPuzzle()
    {
        return $this->puzzle;
    }

    /**
     * Sets the puzzle array
     *
     * If an invalid puzzle is supplied, an empty puzzle is generated instead
     *
     * @param array $puzzle
     * @return bool
     */
    public function setPuzzle(array $puzzle = [])
    {
        if ($this->isValidPuzzleFormat($puzzle)) {
            $this->puzzle = $puzzle;
            $this->setSolution($this->generateEmptyPuzzle());
            return true;
        } else {
            $this->puzzle = $this->generateEmptyPuzzle();
            $this->setSolution($this->generateEmptyPuzzle());
            return false;
        }
    }

    /**
     * Gets the solution
     *
     * @return array
     */
    public function getSolution()
    {
        return $this->solution;
    }

    /**
     * Sets the solution array
     *
     * @param array $solution
     * @return bool
     */
    public function setSolution(array $solution)
    {
        if ($this->isValidPuzzleFormat($solution)) {
            $this->solution = $solution;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Solves the puzzle
     *
     * @return bool
     */
    public function solve()
    {
        if ($this->isSolvable()) {
            $this->solution = $this->calculateSolution($this->puzzle);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets the is solved value
     *
     * @return mixed
     */
    public function isSolved()
    {
        if (!$this->checkConstraints($this->solution)) {
            return false;
        }

        foreach ($this->puzzle as $rowIndex => $row) {
            foreach ($row as $columnIndex => $column) {
                if ($column !== 0) {
                    if ($this->puzzle[$rowIndex][$columnIndex] != $this->solution[$rowIndex][$columnIndex]) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Checks if a puzzle is solvable
     *
     * Only ensures the current puzzle is valid and doesn't violate any constraints
     *
     * @return bool
     */
    public function isSolvable()
    {
        return $this->checkConstraints($this->puzzle, true);
    }

    /**
     * Generates a new random puzzle
     *
     * Difficulty is specified by the number of cells pre-populated in the puzzle, these are assigned randomly and does
     * not necessarily guarantee a difficult or easy puzzle
     *
     * @param int $cellCount
     * @return array|bool
     */
    public function generatePuzzle($cellCount = 15)
    {
        if (!is_integer($cellCount) || $cellCount < 0 || $cellCount > 80) {
            return false;
        }

        if ($cellCount === 0) {
            $this->puzzle = $this->generateEmptyPuzzle();
        } else {
            $this->puzzle = $this->calculateSolution($this->generateEmptyPuzzle());

            $cells = array_rand(range(0, 80), $cellCount);
            $i = 0;

            if (is_integer($cells)) {
                $cells = [$cells];
            }

            foreach ($this->puzzle as &$row) {
                foreach ($row as &$cell) {
                    if (!in_array($i++, $cells)) {
                        $cell = 0;
                    }
                }
            }
        }

        $this->setSolution($this->generateEmptyPuzzle());

        return true;
    }

    /**
     * Check constraints of a puzzle or solution
     *
     * @param array $puzzle
     * @param bool $allowZeros
     *
     * @return bool
     */
    protected function checkConstraints(array $puzzle, $allowZeros = false)
    {
        foreach ($puzzle as $rowIndex => $row) {

            if (!$this->checkContainerForViolations($row, $allowZeros)) {
                return false;
            }

            foreach ($row as $columnIndex => $cell) {

                if ($cell == 0) {
                    continue;
                }

                if (!in_array($cell, [1, 2, 3, 4, 5, 6, 7, 8, 9])) {
                    return false;
                }

                $columns[$columnIndex][] = $cell;

                if ($rowIndex % 3 == 0) {
                    $boxRow = $rowIndex;
                } else {
                    $boxRow = $rowIndex - $rowIndex % 3;
                }

                if ($columnIndex % 3 == 0) {
                    $boxColumn = $columnIndex;
                } else {
                    $boxColumn = $columnIndex - $columnIndex % 3;
                }

                $boxes[$boxRow . $boxColumn][] = $cell;
            }
        }

        if (isset($columns)) {
            foreach ($columns as $column) {
                if (!$this->checkContainerForViolations($column, $allowZeros)) {
                    return false;
                }
            }
        }

        if (isset($boxes)) {
            foreach ($boxes as $box) {
                if (!$this->checkContainerForViolations($box, $allowZeros)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Generates an empty puzzle array
     *
     * @return array
     */
    protected function generateEmptyPuzzle()
    {
        return array_fill(0, ($this->CellSize * $this->CellSize), array_fill(0, ($this->CellSize * $this->CellSize), 0));
    }

    /**
     * Ensures the puzzle array is of the correct size
     *
     * @param array $puzzle
     *
     * @return bool
     */
    protected function isValidPuzzleFormat(array $puzzle)
    {
        if (count($puzzle) != ($this->CellSize * $this->CellSize)) {
            return false;
        }

        foreach ($puzzle as $row) {
            if (count($row) != ($this->CellSize * $this->CellSize)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculates the solution
     *
     * A brute force backtracking algorithm that starts at the 0 value cell closest to A1 on the grid and calculates is
     * available options based on the games constraints. It will then populate the cell with the first option and move
     * on to the next cell by calling it's self recursively. Should it eventually find it's self with no available
     * options for a cell it will 'backtrack' to the previous cell and try the next option until either a solution is
     * found or all options are exhausted.
     *
     * @param array $puzzle
     *
     * @return array|bool
     */
    protected function calculateSolution(array $puzzle)
    {
        while (true) {

            $options = null;

            foreach ($puzzle as $rowIndex => $row) {

                $columnIndex = array_search(0, $row);

                if ($columnIndex === false) {
                    continue;
                }

                $validOptions = $this->getValidOptions($puzzle, $rowIndex, $columnIndex);

                if (count($validOptions) == 0) {
                    return false;
                }

                $options = array(
                    'rowIndex' => $rowIndex,
                    'columnIndex' => $columnIndex,
                    'validOptions' => $validOptions
                );

                break;
            }

            if ($options == null) {
                return $puzzle;
            }

            if (count($options['validOptions']) == 1) {
                $puzzle[$options['rowIndex']][$options['columnIndex']] = current($options['validOptions']);
                continue;
            }

            foreach ($options['validOptions'] as $value) {
                $tempPuzzle = $puzzle;
                $tempPuzzle[$options['rowIndex']][$options['columnIndex']] = $value;
                $result = $this->calculateSolution($tempPuzzle);

                if ($result == true) {
                    return $result;
                }
            }

            return false;
        }
    }

    /**
     * Gets the valid options for a cell based on the constraints of the game
     *
     * @param array $grid
     * @param integer $rowIndex
     * @param integer $columnIndex
     *
     * @return array
     */
    protected function getValidOptions(array $grid, $rowIndex, $columnIndex)
    {
        $invalid = $grid[$rowIndex];

        for ($i = 0; $i < 9; $i++) {
            $invalid[] = $grid[$i][$columnIndex];
        }

        if ($rowIndex % 3 == 0) {
            $boxRow = $rowIndex;
        } else {
            $boxRow = $rowIndex - $rowIndex % 3;
        }

        if ($columnIndex % 3 == 0) {
            $boxColumn = $columnIndex;
        } else {
            $boxColumn = $columnIndex - $columnIndex % 3;
        }

        $invalid = array_unique(
            array_merge(
                $invalid,
                array_slice($grid[$boxRow], $boxColumn, 3),
                array_slice($grid[$boxRow + 1], $boxColumn, 3),
                array_slice($grid[$boxRow + 2], $boxColumn, 3)
            )
        );

        $valid = array_diff(range(1, 9), $invalid);
        shuffle($valid);

        return $valid;
    }

    /**
     * Checks an array for violations
     *
     * A array is deemed to contain violations if it contains any duplicate values, the inclusion of 0 values can be
     * specified via the $allowZeros parameter
     *
     * @param array $container
     * @param bool $allowZeros
     *
     * @return bool
     */
    protected function checkContainerForViolations(array $container, $allowZeros = false)
    {
        if (!$allowZeros && in_array(0, $container)) {
            return false;
        }

        if (($keys = array_keys($container, 0)) !== false) {
            foreach ($keys as $key) {
                unset($container[$key]);
            }
        }

        if (count($container) != count(array_unique($container))) {
            return false;
        }

        return true;
    }
}