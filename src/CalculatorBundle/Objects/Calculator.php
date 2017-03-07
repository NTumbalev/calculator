<?php
/**
 * This file is part of the CalculatorBundle.
 *
 * (c) Nikolay Tumbalev <ntumbalev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CalculatorBundle\Objects;

/**
 * Calculator's class
 */
class Calculator
{
    private $tempValue = '';
    public $expressionArray;
    public $error = false;

    protected $operators = [
        'plus' => '+',
        'minus' => '-',
        'multiply' => '*',
        'divide' => '/',
        'point' => '.'
    ];

    /**
     * Check if given value is operator or not
     */
    public function isOperator($value)
    {
        if (in_array($value, $this->operators)) {
            if ($value === $this->operators['point']) {
                $this->addValue($value);
            } else {
                if ($this->tempValue == '') {
                    return false;
                } else {
                    $this->expressionArray[] = (float)$this->tempValue;
                    $this->tempValue = '';
                    $this->expressionArray[] = $value;
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Add value in the parse process
     * @return void
     */
    public function addValue($value)
    {
        $this->tempValue .= $value;
    }

    /**
     * Calculate sum
     * @return float|null
     */
    public function getSum()
    {
        $simpleExpression = $this->calculateMultiplyAndDivision($this->expressionArray);
        if (count($simpleExpression) > 1) {
            return $this->calculatePlusAndMinus($simpleExpression);
        } else {
            return $simpleExpression[0];
        }
    }

    /**
     * Calculate multiply and devision
     * @return array
     */
    public function calculateMultiplyAndDivision($expressionArray)
    {
        for ($i=0; $i < count($expressionArray); $i++) {
            if ($expressionArray[$i] == '*' || $expressionArray[$i] == '/') {
                $func = array_search($expressionArray[$i], $this->operators);
                $sum = $this->$func($expressionArray[$i-1], $expressionArray[$i+1]);
                $newExpressionArray = $expressionArray;
                $newExpressionArray[$i] = $sum;
                unset($newExpressionArray[$i-1]);
                unset($newExpressionArray[$i+1]);
                $newExpressionArray = array_values($newExpressionArray);
                return $this->calculateMultiplyAndDivision($newExpressionArray);
            }
        }

        return $expressionArray;
    }

    /**
     * Calculate plus and minus
     * @return array|float
     */
    public function calculatePlusAndMinus($expressionArray)
    {
        for ($i=0; $i < count($expressionArray); $i++) {
            if ($i % 2 != 0) {
                $func = array_search($expressionArray[$i], $this->operators);
                $sum = $this->$func($expressionArray[$i-1], $expressionArray[$i+1]);
                $newExpressionArray = $expressionArray;
                $newExpressionArray[$i] = $sum;
                unset($newExpressionArray[$i-1]);
                unset($newExpressionArray[$i+1]);
                $newExpressionArray = array_values($newExpressionArray);
                if (count($newExpressionArray) > 1) {
                    return $this->calculatePlusAndMinus($newExpressionArray);
                } else {
                    return $sum;
                }
            }
        }
    }

    /**
     * Check if expression array is valid
     */
    public function isValid()
    {
        //is there any error set
        if ($this->error === true) {
            return false;
        }

        //add final value
        if ($this->tempValue != '') {
            $this->expressionArray[] = (float)$this->tempValue;
            $this->tempValue = '';
        }
        //check for operators in begining and end
        if (!is_numeric($this->expressionArray[0]) || !is_numeric(end($this->expressionArray))) {
            $this->error = true;
            return false;
        }

        //check if expression contains sequence of number/operator/number
        foreach ($this->expressionArray as $key => $value) {
            if ($key % 2 == 0 && !is_numeric($value)) {
                $this->error = true;
                return false;
            }
        }

        return true;
    }

    private function plus($x, $y)
    {
        return $x + $y;
    }

    private function minus($x, $y)
    {
        return $x - $y;
    }

    private function multiply($x, $y)
    {
        return $x * $y;
    }

    private function divide($x, $y)
    {
        return $x / $y;
    }
}
