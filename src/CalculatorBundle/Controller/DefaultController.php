<?php
/*
 * This file is part of the Nikolay Tumbalev's project.
 *
 * (c) Nikolay Tumbalev <ntumbalev@gmail.com>
 *
 * You don't have permissions to copy and/or change this file without knowing of the owner.
 * Please consider a contact with the file owner.
 */
namespace CalculatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use CalculatorBundle\Objects\Calculator;

/**
 * Default controller for calculator bundle
 *
 * @package Nikolay Tumbalev's project
 * @author  Nikolay Tumbalev <ntumbalev@gmail.com>
 */
class DefaultController extends Controller
{
    /**
     * @Route("/calculator", name="calculator")
     */
    public function calculatorAction(Request $request)
    {
        $sum = null;
        $display = null;
        $error = null;
        if ($request->isMethod("POST")) {
            $params = $request->request->all();
            if (array_key_exists('display', $params)) {
                $display = $params['display'];
                $calculator = new Calculator();

                for ($i=0; $i < strlen($display); $i++) {
                    $value = $display[$i];
                    if (is_numeric($value)) {
                        $calculator->addValue($value);
                    } else {
                        if ($calculator->isOperator($value) === false) {
                            $calculator->error = true;
                            $error = 'error.undefined_operator';
                            break;
                        }
                    }
                }

                if ($calculator->isValid()) {
                    try {
                        $sum = $calculator->getSum();
                    } catch (\Exception $e) {
                        $error = $e->getMessage();
                    }
                } else {
                    $error = 'error.not_valid_expression';
                }
            }
        }

        return $this->render('CalculatorBundle:Default:calculator.html.twig', [
            'sum' => $sum,
            'display' => $display,
            'error' => $error
        ]);
    }
}
