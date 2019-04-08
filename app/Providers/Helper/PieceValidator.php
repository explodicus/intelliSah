<?php

namespace App\Providers\Helper;


use App\Models\Piece;

class PieceValidator
{
    /**
     * @param Piece $piece
     * @param $side
     * @param $positionTo
     * @param bool $existPieceTo
     * @return bool
     */
    public function validatePieceToPosition(Piece $piece, $side, $positionTo, $existPieceTo = false)
    {
        $positionFrom = $this->flipToBottomSide($piece->position, $side);
        $positionTo = $this->flipToBottomSide($positionTo, $side);
        $pieceCode = ucfirst($piece->code);

        return $this->{"validate{$pieceCode}"}($positionFrom, $positionTo, $existPieceTo);
    }

    /**
     * @param array $position
     * @param $side
     * @return array
     * @throws \Exception
     */
    protected function flipToBottomSide(array $position, $side)
    {
        list ($y, $x) = $position; // (2, 1) -> (10, 9)

        switch ($side) {
            case 1:
                $x = 11 - $x;
                $y = 11 - $y;
                break;
            case 2:
                $x1 = $x;
                $x = 11 - $y;
                $y = $x1;
                break;
            case 3:
                break;
            case 4:
                $x1 = $x;
                $x = 11 - $y;
                $y = 11 - $x1;
                break;
            default:
                throw new \Exception("Unexpected side");
        }

        return [$y, $x];
    }

    /**
     * @param $positionFrom
     * @param $positionTo
     * @param $existPieceTo
     * @return bool
     */
    protected function validatePawn($positionFrom, $positionTo, $existPieceTo)
    {
        if ($positionFrom[0] === 10 && $positionFrom[1] === $positionTo[1]) {
            if (!$existPieceTo && $positionFrom[0] - $positionTo[0] < 3) {
                return true;
            }
        }

        if ($positionFrom[0] - $positionTo[0] < 2) {
            if (!$existPieceTo && $positionTo[1] === $positionFrom[1]) {
                return true;
            } else if ($existPieceTo && abs($positionTo[1] - $positionFrom[1]) == 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $positionFrom
     * @param $positionTo
     * @param $existPieceTo
     * @return bool
     */
    protected function validateRook($positionFrom, $positionTo, $existPieceTo)
    {
        if ($positionFrom[0] === $positionTo[0] || $positionTo[1] === $positionFrom[1]) {
            return true;
        }

        return false;
    }

    /**
     * @param $positionFrom
     * @param $positionTo
     * @param $existPieceTo
     * @return bool
     */
    protected function validateBishop($positionFrom, $positionTo, $existPieceTo)
    {
        if (abs($positionFrom[0] - $positionTo[0]) === abs($positionTo[1] - $positionFrom[1])) {
            return true;
        }

        return false;
    }

    /**
     * @param $positionFrom
     * @param $positionTo
     * @param $existPieceTo
     * @return bool
     */
    protected function validateKnight($positionFrom, $positionTo, $existPieceTo)
    {
        if (abs($positionFrom[0] - $positionTo[0]) === 2 && abs($positionTo[1] - $positionFrom[1]) == 1
            || abs($positionFrom[1] - $positionTo[1]) === 2 && abs($positionTo[0] - $positionFrom[0]) == 1
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param $positionFrom
     * @param $positionTo
     * @param $existPieceTo
     * @return bool
     */
    protected function validateKing($positionFrom, $positionTo, $existPieceTo)
    {
        if (abs($positionFrom[0] - $positionTo[0]) < 2 && abs($positionTo[1] - $positionFrom[1]) < 2) {
            return true;
        }

        return false;
    }

    /**
     * @param $positionFrom
     * @param $positionTo
     * @param $existPieceTo
     * @return bool
     */
    protected function validateQueen($positionFrom, $positionTo, $existPieceTo)
    {
        return $this->validateRook($positionFrom, $positionTo, $existPieceTo)
            || $this->validateBishop($positionFrom, $positionTo, $existPieceTo);
    }
}