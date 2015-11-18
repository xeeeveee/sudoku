<?php


use Xeeeveee\Sudoku\Puzzle;

class PuzzleTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testAnEmptySolutionIsGeneratedIfInvalidIsUsed()
    {
        $sudoku = new Puzzle();

        $this->assertEquals($sudoku->getSolution(), [
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0]
        ]);
    }

    public function testInvalidPuzzlesAreIgnored()
    {
        $sudoku = new Puzzle();

        $sudoku->setPuzzle([
            [0, 0, 0, 0, 0, 0, 0, 0, 0]
        ]);

        $this->assertEquals($sudoku->getPuzzle(), [
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0]
        ]);
    }

    public function testRowConstraintsIdentified()
    {
        $sudoku = new Puzzle();

        $sudoku->setPuzzle([
            [1, 1, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0]
        ]);

        $this->assertEquals($sudoku->isSolvable(), false);
    }

    public function testColumnConstraintsIdentified()
    {
        $sudoku = new Puzzle();

        $sudoku->setPuzzle([
            [1, 0, 0, 0, 0, 0, 0, 0, 0],
            [1, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0]
        ]);

        $this->assertEquals($sudoku->isSolvable(), false);
    }

    public function testBoxConstraintsIdentified()
    {
        $sudoku = new Puzzle();

        $sudoku->setPuzzle([
            [1, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 1, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0]
        ]);

        $this->assertEquals($sudoku->isSolvable(), false);
    }

    public function testInvalidValuesIdentified()
    {
        $sudoku = new Puzzle();

        $sudoku->setPuzzle([
            [10, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0]
        ]);

        $this->assertEquals($sudoku->isSolvable(), false);
    }

    public function testIsSolvedGetsSetAppropriately()
    {
        $sudoku = new Puzzle();

        $this->assertEquals($sudoku->isSolved(), false);
        $sudoku->solve();
        $this->assertEquals($sudoku->isSolved(), true);
        $sudoku->setPuzzle();
        $this->assertEquals($sudoku->isSolved(), false);
        $sudoku->solve();
        $this->assertEquals($sudoku->isSolved(), true);
        $sudoku->generatePuzzle();
        $this->assertEquals($sudoku->isSolved(), false);
        $sudoku->solve();
        $this->assertEquals($sudoku->isSolved(), true);
    }

    public function testSolutionMatchesPuzzle()
    {
        $sudoku = new Puzzle(3, [
            [1, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 2, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 3, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 4, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 5, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 6, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 7, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 8, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 9],
        ],
        [
            [1, 7, 8, 6, 2, 3, 4, 9, 5],
            [5, 2, 4, 8, 9, 7, 6, 3, 1],
            [6, 9, 3, 5, 1, 4, 8, 2, 7],
            [9, 5, 2, 4, 3, 8, 1, 7, 6],
            [8, 6, 7, 2, 5, 1, 9, 4, 3],
            [4, 3, 1, 9, 7, 6, 2, 5, 8],
            [2, 1, 9, 3, 8, 5, 7, 6, 4],
            [7, 4, 5, 1, 6, 9, 3, 8, 2],
            [3, 8, 6, 7, 4, 2, 5, 1, 9],
        ]);

        $this->assertEquals($sudoku->isSolved(), true);

        $sudoku = new Puzzle(3, [
            [9, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 8, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 7, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 6, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 5, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 4, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 3, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 2, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 1],
        ],
        [
            [1, 7, 8, 6, 2, 3, 4, 9, 5],
            [5, 2, 4, 8, 9, 7, 6, 3, 1],
            [6, 9, 3, 5, 1, 4, 8, 2, 7],
            [9, 5, 2, 4, 3, 8, 1, 7, 6],
            [8, 6, 7, 2, 5, 1, 9, 4, 3],
            [4, 3, 1, 9, 7, 6, 2, 5, 8],
            [2, 1, 9, 3, 8, 5, 7, 6, 4],
            [7, 4, 5, 1, 6, 9, 3, 8, 2],
            [3, 8, 6, 7, 4, 2, 5, 1, 9],
        ]);

        $this->assertEquals($sudoku->isSolved(), false);
    }

    public function testGeneratePuzzleDifficultyMinimumConstraintsIdentified()
    {
        $sudoku = new Puzzle();

        $this->assertEquals($sudoku->generatePuzzle(0), true);
        $this->assertEquals($sudoku->getPuzzle(), [
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0]
        ]);
    }

    public function testGeneratePuzzleDifficultyLowestPossibleValue()
    {
        $sudoku = new Puzzle();

        $this->assertEquals($sudoku->generatePuzzle(0), true);
        $this->assertEquals($sudoku->getPuzzle(), [
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0]
        ]);
    }

    public function testGeneratePuzzleDifficultyMaximumConstraintsIdentified()
    {
        $sudoku = new Puzzle();

        $this->assertEquals($sudoku->generatePuzzle(81), false);
        $this->assertEquals($sudoku->getPuzzle(), [
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0]
        ]);
    }

    public function testGeneratePuzzleDifficultyHighestPossibleValue()
    {
        $sudoku = new Puzzle();

        $this->assertNotEquals($sudoku->generatePuzzle(80), false);
        $this->assertNotEquals($sudoku->getPuzzle(), [
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0]
        ]);
    }
}