<?php

namespace Xeeeveee\Sudoku;

// TODO: Add isValidSolution() method to check if a solution would complete the puzzle

class Sudoku
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
     * Holds the solved value
     *
     * @var boolean
     */
    protected $isSolved = false;

    /**
     * Sets the puzzle on construction
     *
     * @param array $puzzle
     */
    public function __construct(array $puzzle = [])
    {
        $this->setPuzzle($puzzle);
    }

    /**
     * Returns the puzzle array
     *
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
     *
     * @return bool
     */
    public function setPuzzle(array $puzzle = [])
    {
        $this->isSolved = false;

        if ($this->isValidPuzzleFormat($puzzle)) {
            $this->puzzle = $puzzle;

            return true;
        } else {
            $this->puzzle = $this->generateEmptyPuzzle();

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
            $this->isSolved = true;

            return true;
        } else {
            $this->isSolved = false;

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
     * Gets tge is solved value
     *
     * @return mixed
     */
    public function isSolved()
    {
        return $this->isSolved;
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
        foreach ($this->puzzle as $rowIndex => $row) {

            if (!$this->checkForViolations($row)) {
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
                if (!$this->checkForViolations($column)) {
                    return false;
                }
            }
        }

        if (isset($boxes)) {
            foreach ($boxes as $box) {
                if (!$this->checkForViolations($box)) {
                    return false;
                }
            }
        }

        return true;
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
                        $cell = null;
                    }
                }
            }
        }

        $this->isSolved = false;
        return true;
    }

    /**
     * Generates an empty puzzle array
     *
     * @return array
     */
    protected function generateEmptyPuzzle()
    {
        return array_fill(0, 9, array_fill(0, 9, 0));
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
        if (count($puzzle) != 9) {
            return false;
        }

        foreach ($puzzle as $row) {
            if (count($row) != 9) {
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
     * A array is deemed to contain violations if it contains any duplicate values once all (int) 0 values have been
     * removed.
     *
     * @param array $container
     *
     * @return bool
     */
    protected function checkForViolations(array $container)
    {
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