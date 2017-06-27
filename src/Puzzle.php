<?php

namespace Xeeeveee\Sudoku;

class Puzzle
{
    /**
     * Holds the puzzle
     *
     * @var array
     */
    protected $puzzle = [];

    protected $puzzleColumns = [];

    protected $puzzleBoxes = [];

    /**
     * Holds the solution
     *
     * @var array
     */
    protected $solution = [];

    protected $solutionColumns = [];

    protected $solutionBoxes = [];

    /**
     * The size of the grid
     *
     * @var int
     */
    protected $cellSize = 3;

    /**
     * Box lookup by row and column index
     *
     * @var array
     */
    protected $boxLookup;

    /**
     * Sets the puzzle on construction
     *
     * @param array $puzzle
     * @param array $solution
     */
    public function __construct($cellSize = 3, array $puzzle = [], array $solution = [])
    {
        $this->setCellSize($cellSize, $puzzle, $solution);
    }

    /**
     * Gets the grid size
     * 
     * @return int
     */
    public function getCellSize()
    {
        return $this->cellSize;
    }

    /**
     * Sets the grid size
     *
     * Changing the grid size will essentially reset the object, setting the $puzzle & $solution properties to valid
     * empty values. The cell size must be 2 or greater.
     *
     * @param int $cellSize
     * @param array $puzzle
     * @param array $solution
     * @return bool
     */
    public function setCellSize($cellSize, array $puzzle = [], array $solution = [])
    {
        if(is_integer($cellSize) && $cellSize > 1) {
            $this->cellSize = $cellSize;
            $this->setPuzzle($puzzle);
            $this->setSolution($solution);
            return true;
        }

        return false;
    }

    /**
     * Gets the grid size
     *
     * @return int
     */
    public function getGridSize()
    {
        return $this->cellSize * $this->cellSize;
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
            $this->setSolution($this->puzzle);
            $this->prepareReferences();
            return true;
        } else {
            $this->puzzle = $this->generateEmptyPuzzle();
            $this->setSolution($this->puzzle);
            $this->prepareReferences();
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
            $this->prepareReferences(false);
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
            if($this->calculateSolution($this->solution)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the is solved value
     *
     * @return mixed
     */
    public function isSolved()
    {
        if (!$this->checkConstraints($this->solution, $this->solutionColumns, $this->solutionBoxes)) {
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
        return $this->checkConstraints($this->puzzle, $this->puzzleColumns, $this->puzzleBoxes, true);
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
        if (!is_integer($cellCount) || $cellCount < 0 || $cellCount > $this->getCellCount()) {
            return false;
        }

        $this->setPuzzle($this->generateEmptyPuzzle());

        if ($cellCount > 0) {
            $this->solve();
            $cells = array_rand(range(0, ($this->getCellCount() -1)), $cellCount);
            $i = 0;

            if (is_integer($cells)) {
                $cells = [$cells];
            }

            foreach ($this->solution as &$row) {
                foreach ($row as &$cell) {
                    if (!in_array($i++, $cells)) {
                        $cell = 0;
                    }
                }
            }

            // Breaks reference between puzzle & solution
            $this->puzzle = unserialize(serialize($this->solution));
        }

        $this->prepareReferences();

        return true;
    }

    /**
     * Check constraints of a puzzle or solution
     *
     * @param array $rows
     * @param array $columns
     * @param array $boxes
     * @param bool $allowZeros
     *
     * @return bool
     */
    protected function checkConstraints($rows, $columns, $boxes, $allowZeros = false)
    {
        foreach ($rows as $rowIndex => $row) {
            if (!$this->checkContainerForViolations($row, $allowZeros)) {
                return false;
            }

            foreach ($columns as $columnIndex => $column) {

                if (!$this->checkContainerForViolations($column, $allowZeros)) {
                    return false;
                }

                if (!$this->checkContainerForViolations($boxes[$this->boxLookup[$rowIndex][$columnIndex]], $allowZeros)) {
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
        return array_fill(0, $this->getGridSize(), array_fill(0, $this->getGridSize(), 0));
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
        if (!is_array($puzzle) || count($puzzle) != $this->getGridSize()) {
            return false;
        }

        foreach ($puzzle as $row) {
            if (count($row) != $this->getGridSize()) {
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
     * @return bool
     */
    protected function calculateSolution(array $puzzle)
    {
        $continue = true;

        while ($continue) {

            $options = null;

            foreach ($puzzle as $rowIndex => $row) {

                $columnIndex = array_search(0, $row);

                if ($columnIndex === false) {
                    continue;
                }

                $validOptions = $this->getValidOptions($rowIndex, $columnIndex);

                if (count($validOptions) == 0) {
                    return false;
                }

                break;
            }

            if (!isset($validOptions) || empty($validOptions)) {
                return $puzzle;
            }

            foreach ($validOptions as $key => $value) {
                $puzzle[$rowIndex][$columnIndex] = $value;
                $result = $this->calculateSolution($puzzle);

                if ($result == true) {
                    return $result;
                } else {
                    $puzzle[$rowIndex][$columnIndex] = 0;
                }
            }

            $continue = false;
        }

        return false;
    }

    /**
     * Gets the valid options for a cell based on the constraints of the game
     *
     * @param integer $rowIndex
     * @param integer $columnIndex
     *
     * @return array
     */
    protected function getValidOptions($rowIndex, $columnIndex)
    {
        $invalid = array_merge($this->solution[$rowIndex], $this->solutionColumns[$columnIndex], $this->solutionBoxes[$this->boxLookup[$rowIndex][$columnIndex]]);
        $invalid = array_flip(array_flip($invalid));

        $valid = array_diff(range(1, $this->getGridSize()), $invalid);
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

        // array_flip(array_flip()) is significantly faster than array_unique()
        $flippedContainer = array_flip($container);
        $uniqueContainer = array_flip($flippedContainer);

        if (count($container) != count($uniqueContainer)) {
            return false;
        }

        foreach(range(1, $this->getGridSize()) as $index) {
            unset($flippedContainer[$index]);
        }

        if(!empty($flippedContainer)) {
            return false;
        }

        return true;
    }

    /**
     * Gets the total number of cells in the puzzle
     *
     * @return int
     */
    protected function getCellCount()
    {
        return ($this->getGridSize() * $this->getGridSize());
    }

    /**
     * Prepares references
     *
     * @param bool $puzzle
     */
    protected function prepareReferences($puzzle = true)
    {
        if($puzzle) {
            $source = &$this->puzzle;
            $columns = &$this->puzzleColumns;
            $boxes = &$this->puzzleBoxes;
        } else {
            $source = &$this->solution;
            $columns = &$this->solutionColumns;
            $boxes = &$this->solutionBoxes;
        }

        $this->setColumns($source, $columns);
        $this->setBoxes($source, $boxes);
    }

    /**
     * Sets a columns array linked to the puzzle by reference
     *
     * Rebuilds the array from scratch to prevent unwanted cells lingering when shrinking the cell count
     *
     * @param array $source
     * @param array $columns
     */
    protected function setColumns(array &$source, array &$columns)
    {
        $columns = [];
        for($i = 0; $i < $this->getGridSize(); $i++)
        {
            for($j = 0; $j < $this->getGridSize(); $j++)
            {
                $columns[$j][$i] = &$source[$i][$j];
            }
        }
    }

    /**
     * Sets a boxes array linked to the puzzle by reference
     *
     * Rebuilds the array from scratch to prevent unwanted cells lingering when shrinking the cell count
     *
     * @param array $source
     * @param array $boxes
     */
    protected function setBoxes(array &$source, array &$boxes)
    {
        $boxes = [];
        for($i = 0; $i < $this->getGridSize(); $i++)
        {
            for($j = 0; $j < $this->getGridSize(); $j++)
            {
                $row = floor(($i ) / $this->cellSize);
                $column =  floor(($j ) / $this->cellSize);
                $box = (int) floor($row * $this->cellSize + $column);
                $cell = ($i % $this->cellSize) * ($this->cellSize) + ($j % $this->cellSize);

                $boxes[$box][$cell] = &$source[$i][$j];
                $this->boxLookup[$i][$j] = $box;
            }
        }
    }
}