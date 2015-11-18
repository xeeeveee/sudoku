[![Build Status](https://travis-ci.org/xeeeveee/sudoku.svg?branch=master)](https://travis-ci.org/xeeeveee/sudoku)

# PHP Sudoku Generator & Solver

A PHP Sudoku generate and solver implemented via a bruteforce backtracking algorithm.

## Installation

Install via composer with `php composer require xeeeveee/sudoku:*`

## Usage

### TL;DR full examples

```php
    // Generate a new puzzle
    $puzzle = new Xeeeveee\Sudoku\Puzzle();
    $puzzle->generatePuzzle();
    $puzzle = $puzzle->getPuzzle();

    // Solve a pre-determined puzzle
    $puzzle = new Xeeeveee\Sudoku\Puzzle($puzzle);
    $puzzle->solve();
    $solution = $puzzle->getSolution();

    // Check a puzzle is solvable
    $puzzle = new Xeeeveee\Sudoku\Puzzle();
    $puzzle->setPuzzle($puzzle);
    $solvable = $puzzle->isSolvable();

    // Check a puzzle is solved
    $puzzle = new Xeeeveee\Sudoku\Puzzle();
    $puzzle->setPuzzle($puzzle);
    $puzzle->solve($puzzle);
    $solved = $puzzle->isSolved();
    
    // Generate a puzzle with a different cell size
    $puzzle = new Xeeeveee\Sudoku\Puzzle();
    $puzzle->setCellSize(5); // 25 * 25 grid
    $puzzle->generatePuzzle();

    // Setting properties in the constructor
    $puzzle = new Xeeeveee\Sudoku\Puzzle($cellSize, $puzzle, $solution);
```

### Generator

Once an instance has been initialized you can generate a new sudoku puzzle by calling the `generatePuzzle()` method as below:

```php
    $puzzle = new Xeeeveee\Sudoku\Puzzle();
    $puzzle->generatePuzzle();
```

You can also specify the difficulty of the puzzle to generate by passing an integer between 0 and 81. This represents how many of the cells will be pre-populated in the puzzle. For example, the below snippet should generate a puzzle with 25 of the cells pre-populated.

```php
    $puzzle = new Xeeeveee\Sudoku\Puzzle();
    $puzzle->generatePuzzle(25);
```

### Solver

Solving a puzzle is as simple as calling the `solve()` method on the object, which will return either `true` or `false` depending on the outcome, see below for example:

```php
    $puzzle = new Xeeeveee\Sudoku\Puzzle();
    $puzzle->generatePuzzle(25);
    $puzzle->solve();
```

You can use the `isSolved()` method to check if the object contains a solved solution, and use the `getSolution` method to retrieve the array, a more complete example might look like the below:

```php
    $puzzle = new Xeeeveee\Sudoku\Puzzle();
    $puzzle->generatePuzzle(25);

    if($puzzle->isSolvable() && $puzzle->isSolved() !== true) {
        $puzzle->solve();
    }

    $solution = $puzzle->getSolution();
```

### Puzzle & solution format

The puzzle and solution is represented as 3 dimensional array, effectively 9 rows with 9 columns where blank values are represented as `0`. The definition for a complete empty puzzle or solution would look like the below:

```php
    $puzzle = [
        [0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0],
    ];
```

### Available methods

**integer** `getCellSize()`
Returns cell size of the puzzle.

**boolean** `setCellSize()`
Sets the cell size of the puzzle. **Note** - This will reset the `$puzzle` and `$solution` properties on the object.

**integer** `getGridSize()`
Returns cell size of the puzzle. **Note** - This is calculated based on the `$cellSize` property.

**array** `getPuzzle()`
Returns the puzzle array.

**boolean** `setPuzzle(array $puzzle = [])`
Sets the puzzle array - If the `$puzzle` parameter is omitted or an invalid array structure is pass, a empty grid will be generated and false will be returned.
**Note** - Setting the puzzle always resets the solution to an empty grid.

**array** `getSolution()`
Returns the solution array.

**boolean** `setSolution(array $solution)`
Sets the solution, if the `$solution` parameter supplied is an invalid format false is returned and the solution is not modified.

**boolean** `solve()`
Attempts to solve the puzzle.

**boolean** `isSolved()`
Returns true if a the solution is valid for the current puzzle.

**boolean** `isSolvable()`
Returns true if the puzzle is solvable - This is significantly quicker then actually solving the puzzle.

**boolean** `generatePuzzle($cellCount = 15)`
Generates a new puzzle, the `$cellCount` parameter specifies how many cells will be pre-populated, effectively manipulating the difficulty. 0 - 81 are valid values for `$cellCount` if any other value is supplied false is returned.
**Note** - Generating a puzzle always resets the solution to an empty grid.