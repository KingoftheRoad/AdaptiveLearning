<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LearningsObjectives;
use App\Constants\DbConstant as cn;

class LearningObjectivesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '1.1',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve quadratic equations by the factor method',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve quadratic equations by the factor method',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '以因式法解二次方程',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 1,
                cn::LEARNING_OBJECTIVES_CODE_COL => '01'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '1.2',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'form quadratic equations from given roots',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'form quadratic equations from given roots',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '由已知根建立二次方程',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 1,
                cn::LEARNING_OBJECTIVES_CODE_COL => '02'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '1.3',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve quadratic equation by plotting the graph of the parabola and reading the x-intercepts',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve quadratic equation by plotting the graph of the parabola and reading the x-intercepts',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '通過繪製拋物線圖並讀取 x 截距來求解二次方程',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 1,
                cn::LEARNING_OBJECTIVES_CODE_COL => '03'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '1.4',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve quadratic equations by the quadratic formula',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve quadratic equations by the quadratic formula',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '以二次公式解二次方程',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 1,
                cn::LEARNING_OBJECTIVES_CODE_COL => '04'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '1.5',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the relations between the discriminant of a quadratic equation and the nature of its roots',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the relations between the discriminant of a quadratic equation and the nature of its roots',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解二次方程的判別式與其根的性質之關係',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 1,
                cn::LEARNING_OBJECTIVES_CODE_COL => '05'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '1.6',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve problems involving quadratic equations',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve problems involving quadratic equations',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '解涉及二次方程的應用題',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 1,
                cn::LEARNING_OBJECTIVES_CODE_COL => '06'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '1.7',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the relations between the roots and coefficients and form quadratic equations using these relations',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the relations between the roots and coefficients and form quadratic equations using these relations',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解根與係數的關係及以此關係建立二次方程',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 1,
                cn::LEARNING_OBJECTIVES_CODE_COL => '07'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '1.8',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'appreciate the development of the number systems including the system of complex numbers',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'appreciate the development of the number systems including the system of complex numbers',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '欣賞數系（包括複數系）的發展',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 1,
                cn::LEARNING_OBJECTIVES_CODE_COL => '08'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '1.9',
                cn::LEARNING_OBJECTIVES_TITLE_COL => "perform addition, subtraction, multiplication and division of complex numbers",
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => "perform addition, subtraction, multiplication and division of complex numbers",
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => "進行複數的加、減、乘和除運算",
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 1,
                cn::LEARNING_OBJECTIVES_CODE_COL => '09'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '2.1',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'recognise the intuitive concepts of functions, domains and co-domains, independent and dependent variables',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'recognise the intuitive concepts of functions, domains and co-domains, independent and dependent variables',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '認識函數、定義域和上域、自變量和應變量的直觀概念',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 2,
                cn::LEARNING_OBJECTIVES_CODE_COL => '01'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '2.2',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'recognise the notation of functions and use tabular, algebraic and graphical methods to represent functions',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'recognise the notation of functions and use tabular, algebraic and graphical methods to represent functions',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '認識函數的記法及運用表列、代數和圖像方法來表達函數',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 2,
                cn::LEARNING_OBJECTIVES_CODE_COL => '02'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '2.3',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the features of the graphs of quadratic functions',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the features of the graphs of quadratic functions',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解二次函數圖像的特徵',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 2,
                cn::LEARNING_OBJECTIVES_CODE_COL => '03'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '2.4',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'find the maximum and minimum values of quadratic functions by the algebraic method',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'find the maximum and minimum values of quadratic functions by the algebraic method',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '以代數方法求二次函數的極大值和極小值',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 2,
                cn::LEARNING_OBJECTIVES_CODE_COL => '04'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '3.1',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the definitions of rational indices',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the definitions of rational indices',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解有理數指數的定義',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 3,
                cn::LEARNING_OBJECTIVES_CODE_COL => '01'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '3.2',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the laws of rational indices',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the laws of rational indices',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解有理指數的定律',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 3,
                cn::LEARNING_OBJECTIVES_CODE_COL => '02'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '3.3',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the definition and properties of logarithms (including the change of base)',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the definition and properties of logarithms (including the change of base)',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解對數的定義及其性質（包括換底公式）',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 3,
                cn::LEARNING_OBJECTIVES_CODE_COL => '03'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '3.4',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the properties of exponential functions and logarithmic functions and recognise the features of their graphs',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the properties of exponential functions and logarithmic functions and recognise the features of their graphs',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解指數函數和對數函數的性質及認識其圖像的特徵',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 3,
                cn::LEARNING_OBJECTIVES_CODE_COL => '04'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '3.5',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve exponential equations and logarithmic equations',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve exponential equations and logarithmic equations',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '解指數方程和對數方程',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 3,
                cn::LEARNING_OBJECTIVES_CODE_COL => '05'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '3.6',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'appreciate the applications of logarithms in real-life situations',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'appreciate the applications of logarithms in real-life situations',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '欣賞對數在現實生活情境中的應用',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 3,
                cn::LEARNING_OBJECTIVES_CODE_COL => '06'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '3.7',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'appreciate the development of the concepts of logarithms',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'appreciate the development of the concepts of logarithms',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '欣賞對數概念的發展',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 3,
                cn::LEARNING_OBJECTIVES_CODE_COL => '07'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '4.1',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'perform division of polynomials',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'perform division of polynomials',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '進行多項式除法',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 4,
                cn::LEARNING_OBJECTIVES_CODE_COL => '01'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '4.2',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the remainder theorem',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the remainder theorem',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解餘式定理',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 4,
                cn::LEARNING_OBJECTIVES_CODE_COL => '02'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '4.3',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the factor theorem',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the factor theorem',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解因式定理',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 4,
                cn::LEARNING_OBJECTIVES_CODE_COL => '03'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '4.4',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the concepts of the greatest common divisor and the least common multiple of polynomials',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the concepts of the greatest common divisor and the least common multiple of polynomials',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解最大公因式和最小公倍式的概念',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 4,
                cn::LEARNING_OBJECTIVES_CODE_COL => '04'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '4.5',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'perform addition, subtraction, multiplication and division of rational functions',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'perform addition, subtraction, multiplication and division of rational functions',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '進行有理函數的加、減、乘和除',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 4,
                cn::LEARNING_OBJECTIVES_CODE_COL => '05'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '5.1',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'use the graphical method to solve simultaneous equations in two unknowns, one linear and one quadratic in the form y = ax2 + bx + c',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'use the graphical method to solve simultaneous equations in two unknowns, one linear and one quadratic in the form y = ax2 + bx + c',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '運用圖解法解分別為二元一次及二元二次的聯立方程，其中二元二次方程只限於y=ax2+bx+c的形式',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 5,
                cn::LEARNING_OBJECTIVES_CODE_COL => '01'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '5.2',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'use the algebraic method to solve simultaneous equations in two unknowns, one linear and one quadratic',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'use the algebraic method to solve simultaneous equations in two unknowns, one linear and one quadratic',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '運用代數方法解分別為二元一次及二元二次的聯立方程',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 5,
                cn::LEARNING_OBJECTIVES_CODE_COL => '02'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '5.3',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve equations (including fractional equations, exponential equations, logarithmic equations and trigonometric equations) which can be transformed into quadratic equations',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve equations (including fractional equations, exponential equations, logarithmic equations and trigonometric equations) which can be transformed into quadratic equations',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '解可變換為二次方程的方程（其中包括分式方程、指數方程、對數方程和三角方程）',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 5,
                cn::LEARNING_OBJECTIVES_CODE_COL => '03'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '5.4',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve problems involving equations which can be transformed into quadratic equations',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve problems involving equations which can be transformed into quadratic equations',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '解涉及可變換為二次方程的方程之應用題',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 5,
                cn::LEARNING_OBJECTIVES_CODE_COL => '04'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '6.1',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand direct variations and inverse variations, and their applications to solving real-life problems',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand direct variations and inverse variations, and their applications to solving real-life problems',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解正變和反變及其在解現實生活問題時的應用',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 6,
                cn::LEARNING_OBJECTIVES_CODE_COL => '01'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '6.2',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the graphs of direct and inverse variations',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the graphs of direct and inverse variations',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解正變和反變的圖像',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 6,
                cn::LEARNING_OBJECTIVES_CODE_COL => '02'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '6.3',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand joint and partial variations, and their applications to solving real-life problems',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand joint and partial variations, and their applications to solving real-life problems',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解聯變和部分變及其在解現實生活問題時的應用',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 6,
                cn::LEARNING_OBJECTIVES_CODE_COL => '03'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '7.1',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the concept and the properties of arithmetic sequences',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the concept and the properties of arithmetic sequences',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解等差數列的概念及其性質',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 7,
                cn::LEARNING_OBJECTIVES_CODE_COL => '01'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '7.2',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the general term of an arithmetic sequence',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the general term of an arithmetic sequence',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解等差數列的通項',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 7,
                cn::LEARNING_OBJECTIVES_CODE_COL => '02'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '7.3',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the concept and the properties of geometric sequences',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the concept and the properties of geometric sequences',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解等比數列的概念及其性質',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 7,
                cn::LEARNING_OBJECTIVES_CODE_COL => '03'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '7.4',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the general term of a geometric sequence',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the general term of a geometric sequence',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解等比數列的通項',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 7,
                cn::LEARNING_OBJECTIVES_CODE_COL => '04'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '7.5',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the general formulae of the sum to a finite number of terms of an arithmetic sequence and a geometric sequence and use the formulae to solve related problems',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the general formulae of the sum to a finite number of terms of an arithmetic sequence and a geometric sequence and use the formulae to solve related problems',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解等差數列和等比數列的有限項求和公式及運用該些公式解有關的應用題',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 7,
                cn::LEARNING_OBJECTIVES_CODE_COL => '05'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '7.6',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'explore the general formulae of the sum to infinity for certain geometric sequences and use the formulae to solve related problems',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'explore the general formulae of the sum to infinity for certain geometric sequences and use the formulae to solve related problems',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '探究某些等比數列的無限項求和公式及運用該公式解有關的應用題',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 7,
                cn::LEARNING_OBJECTIVES_CODE_COL => '06'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '7.7',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve related real-life problems',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve related real-life problems',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '解現實生活中相關的應用題',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 7,
                cn::LEARNING_OBJECTIVES_CODE_COL => '07'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '8.1',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve compound linear inequalities in one unknown',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve compound linear inequalities in one unknown',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '解複合一元一次不等式',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 8,
                cn::LEARNING_OBJECTIVES_CODE_COL => '01'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '8.2',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve quadratic inequalities in one unknown by the graphical method',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve quadratic inequalities in one unknown by the graphical method',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '以圖解法解一元二次不等式',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 8,
                cn::LEARNING_OBJECTIVES_CODE_COL => '02'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '8.3',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve quadratic inequalities in one unknown by the algebraic method',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve quadratic inequalities in one unknown by the algebraic method',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '以代數方法解一元二次不等式',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 8,
                cn::LEARNING_OBJECTIVES_CODE_COL => '03'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '8.4',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'represent the graphs of linear inequalities in two unknowns in the rectangular coordinate plane',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'represent the graphs of linear inequalities in two unknowns in the rectangular coordinate plane',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '在直角坐標平面上表示二元一次不等式的圖像',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 8,
                cn::LEARNING_OBJECTIVES_CODE_COL => '04'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '8.5',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve systems of linear inequalities in two unknowns',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve systems of linear inequalities in two unknowns',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '解聯立二元一次不等式',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 8,
                cn::LEARNING_OBJECTIVES_CODE_COL => '05'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '8.6',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve linear programming problems',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve linear programming problems',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '解線性規畫應用題',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 8,
                cn::LEARNING_OBJECTIVES_CODE_COL => '06'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '9.1',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'sketch and compare graphs of various types of functions including constant, linear, quadratic, trigonometric, exponential and logarithmic functions',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'sketch and compare graphs of various types of functions including constant, linear, quadratic, trigonometric, exponential and logarithmic functions',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '描繪及比較不同函數的圖像，包括常值函數、線性函數、二次函數、三角函數、指數函數和對數函數的圖像',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 9,
                cn::LEARNING_OBJECTIVES_CODE_COL => '01'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '9.2',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve the equation f (x) = k using the graph of y = f (x)',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve the equation f (x) = k using the graph of y = f (x)',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '運用y=f(x)的圖像解方程f(x)=k',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 9,
                cn::LEARNING_OBJECTIVES_CODE_COL => '02'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '9.3',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve the inequalities f (x) > k , f (x) < k , f (x) is larger than or equal to k and f (x) is smaller than or equal to k using the graph of y = f (x)',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve the inequalities f (x) > k , f (x) < k , f (x) is larger than or equal to k and f (x) is smaller than or equal to k using the graph of y = f (x)',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '運用y=f(x)的圖像解不等式f(x)>k、f(x)<k、f(x)大於或等於k和f(x)少於或等於k',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 9,
                cn::LEARNING_OBJECTIVES_CODE_COL => '03'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '9.4',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the transformations of the function f (x) including f (x) + k , f (x + k) , k f (x) and f (kx) from tabular, symbolic and graphical perspectives',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the transformations of the function f (x) including f (x) + k , f (x + k) , k f (x) and f (kx) from tabular, symbolic and graphical perspectives',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '從表列、符號和圖像的角度理解函數f(x)的變換，包括f(x)+k、f(x+k)、kf(x)和f(kx)',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 9,
                cn::LEARNING_OBJECTIVES_CODE_COL => '04'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '10.1',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the properties of chords and arcs of a circle',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the properties of chords and arcs of a circle',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解圓的弦和弧的特性',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 10,
                cn::LEARNING_OBJECTIVES_CODE_COL => '01'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '10.2',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the angle properties of a circle',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the angle properties of a circle',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解圓的角屬性',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 10,
                cn::LEARNING_OBJECTIVES_CODE_COL => '02'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '10.3',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the properties of a cyclic quadrilateral',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the properties of a cyclic quadrilateral',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解循環四邊形的性質',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 10,
                cn::LEARNING_OBJECTIVES_CODE_COL => '03'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '10.4',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the tests for concyclic points and cyclic quadrilaterals',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the tests for concyclic points and cyclic quadrilaterals',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解同環點和循環四邊形的檢驗',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 10,
                cn::LEARNING_OBJECTIVES_CODE_COL => '04'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '10.5',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the properties of tangents to a circle and angles in the alternate segments',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the properties of tangents to a circle and angles in the alternate segments',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '了解圓的切線和交替線段中的角度的屬性',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 10,
                cn::LEARNING_OBJECTIVES_CODE_COL => '05'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '10.6',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'use the basic properties of circles to perform simple geometric proofs',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'use the basic properties of circles to perform simple geometric proofs',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '使用圓的基本屬性進行簡單的幾何證明',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 10,
                cn::LEARNING_OBJECTIVES_CODE_COL => '06'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '11.1',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the concept of loci',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the concept of loci',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解軌蹟的概念',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 11,
                cn::LEARNING_OBJECTIVES_CODE_COL => '01'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '11.2',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'describe and sketch the locus of points satisfying given conditions',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'describe and sketch the locus of points satisfying given conditions',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '描述和勾畫滿足給定條件的點的軌跡',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 11,
                cn::LEARNING_OBJECTIVES_CODE_COL => '02'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '11.3',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'describe the locus of points with algebraic equations',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'describe the locus of points with algebraic equations',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '用代數方程描述點的軌跡',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 11,
                cn::LEARNING_OBJECTIVES_CODE_COL => '03'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '12.1',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the equation of a straight line',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the equation of a straight line',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解直線方程',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 12,
                cn::LEARNING_OBJECTIVES_CODE_COL => '01'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '12.2',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the possible intersection of two straight lines',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the possible intersection of two straight lines',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解兩直線相交的各種可能情況',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 12,
                cn::LEARNING_OBJECTIVES_CODE_COL => '02'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '12.3',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the equation of a circle',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the equation of a circle',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解圓方程',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 12,
                cn::LEARNING_OBJECTIVES_CODE_COL => '03'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '12.4',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'find the coordinates of the intersections of a straight line and a circle and understand the possible intersection of a straight line and a circle',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'find the coordinates of the intersections of a straight line and a circle and understand the possible intersection of a straight line and a circle',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '求直線與圓交點的坐標及理解直線與圓相交的各種可能情況',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 12,
                cn::LEARNING_OBJECTIVES_CODE_COL => '04'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '13.1',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the functions sine, cosine and tangent, and their graphs and properties, including maximum and minimum values and periodicity',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the functions sine, cosine and tangent, and their graphs and properties, including maximum and minimum values and periodicity',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解正弦、餘弦和正切函數及其圖像和性質，包括極大值、極小值和週期性',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 13,
                cn::LEARNING_OBJECTIVES_CODE_COL => '01'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '13.2',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve the trigonometric equations a sin ❑ = b , a cos ❑ = b , a tan ❑ = b (solutions in the interval from 0 to 360 ) and other trigonometric equations (solutions in the interval from 0 to 360 )',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve the trigonometric equations a sin ❑ = b , a cos ❑ = b , a tan ❑ = b (solutions in the interval from 0 to 360 ) and other trigonometric equations (solutions in the interval from 0 to 360 )',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '解三角方程asinq=b、acosq=b、atanq=b（其解限於0°至360°區間）和其他的三角方程（其解限於0°至360°區間）',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 13,
                cn::LEARNING_OBJECTIVES_CODE_COL => '02'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '13.3',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the formula ½ ab sin C for areas of triangles',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the formula ½ ab sin C for areas of triangles',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解三角形面積公式½absinC',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 13,
                cn::LEARNING_OBJECTIVES_CODE_COL => '03'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '13.4',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the sine and cosine formulae',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the sine and cosine formulae',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解正弦和餘弦公式',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 13,
                cn::LEARNING_OBJECTIVES_CODE_COL => '04'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '13.5',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand Heron’s formula',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand Heron’s formula',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解希羅公式',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 13,
                cn::LEARNING_OBJECTIVES_CODE_COL => '05'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '13.6',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the concept of projection',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the concept of projection',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解投影的概念',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 13,
                cn::LEARNING_OBJECTIVES_CODE_COL => '06'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '13.7',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the angle between a line and a plane, and the angle between 2 planes',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the angle between a line and a plane, and the angle between 2 planes',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解一線與一平面的相交角和兩平面的相交角',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 13,
                cn::LEARNING_OBJECTIVES_CODE_COL => '07'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '13.8',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the theorem of three perpendiculars',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the theorem of three perpendiculars',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解三垂線定理',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 13,
                cn::LEARNING_OBJECTIVES_CODE_COL => '08'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '13.9',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve related 2-dimensional and 3-dimensional problems',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve related 2-dimensional and 3-dimensional problems',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '解二維和三維空間中相關的應用題',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 13,
                cn::LEARNING_OBJECTIVES_CODE_COL => '09'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '14.1',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the addition rule and multiplication rule in the counting principle',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the addition rule and multiplication rule in the counting principle',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解計數原理的加法法則和乘法法則',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 14,
                cn::LEARNING_OBJECTIVES_CODE_COL => '01'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '14.2',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the concept and notation of permutation',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the concept and notation of permutation',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解排列的概念和記法',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 14,
                cn::LEARNING_OBJECTIVES_CODE_COL => '02'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '14.3',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve problems on the permutation of distinct objects without repetition',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve problems on the permutation of distinct objects without repetition',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '解不同物件的無重排列應用題',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 14,
                cn::LEARNING_OBJECTIVES_CODE_COL => '03'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '14.4',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the concept and notation of combination',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the concept and notation of combination',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解組合的概念和記法',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 14,
                cn::LEARNING_OBJECTIVES_CODE_COL => '04'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '14.5',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'solve problems on the combination of distinct objects without repetition',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'solve problems on the combination of distinct objects without repetition',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '解不同物件的無重組合應用題',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 14,
                cn::LEARNING_OBJECTIVES_CODE_COL => '05'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '15.1',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'recognise the notation of set language including union, intersection and complement',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'recognise the notation of set language including union, intersection and complement',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '認識集合語言的符號，包括並、交和補',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 15,
                cn::LEARNING_OBJECTIVES_CODE_COL => '01'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '15.2',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the addition law of probability and the concepts of mutually exclusive events and complementary events',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the addition law of probability and the concepts of mutually exclusive events and complementary events',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解概率的加法和互斥事件和互補事件的概念',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 15,
                cn::LEARNING_OBJECTIVES_CODE_COL => '02'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '15.3',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the multiplication law of probability and the concept of independent events',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the multiplication law of probability and the concept of independent events',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解概率乘法規律和獨立事件的概念',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 15,
                cn::LEARNING_OBJECTIVES_CODE_COL => '03'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '15.4',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'recognise the concept and notation of conditional probability',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'recognise the concept and notation of conditional probability',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '認識條件概率的概念和符號',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 15,
                cn::LEARNING_OBJECTIVES_CODE_COL => '04'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '15.5',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'use permutation and combination to solve problems related to probability',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'use permutation and combination to solve problems related to probability',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '使用排列組合解決概率相關問題',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 15,
                cn::LEARNING_OBJECTIVES_CODE_COL => '05'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '16.1',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the concept of dispersion',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the concept of dispersion',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解離差的概念',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 16,
                cn::LEARNING_OBJECTIVES_CODE_COL => '01'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '16.2',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the concepts of range and inter-quartile range',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the concepts of range and inter-quartile range',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解分佈域和四分位數間距的概念',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 16,
                cn::LEARNING_OBJECTIVES_CODE_COL => '02'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '16.3',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'construct and interpret the box-and-whisker diagram and use it to compare the distributions of different sets of data',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'construct and interpret the box-and-whisker diagram and use it to compare the distributions of different sets of data',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '製作及闡釋框線圖及運用框線圖比較不同組別的數據分佈',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 16,
                cn::LEARNING_OBJECTIVES_CODE_COL => '03'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '16.4',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the concept of standard deviation for both grouped and ungrouped data sets',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the concept of standard deviation for both grouped and ungrouped data sets',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解分組數據和不分組數據的標準差之概念',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 16,
                cn::LEARNING_OBJECTIVES_CODE_COL => '04'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '16.5',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'compare the dispersions of different sets of data using appropriate measures',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'compare the dispersions of different sets of data using appropriate measures',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '運用合適的量度方法比較不同組別數據的離差',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 16,
                cn::LEARNING_OBJECTIVES_CODE_COL => '05'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '16.6',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the applications of standard deviation to real-life problems involving standard scores and the normal distribution',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the applications of standard deviation to real-life problems involving standard scores and the normal distribution',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解標準差在涉及標準分和正態分佈的現實生活問題時的應用',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 16,
                cn::LEARNING_OBJECTIVES_CODE_COL => '06'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '16.7',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'understand the effect of the following operations on the dispersion of the data: (i) adding a common constant to each item of the set of data (ii) multiplying each item of the set of data by a common constant',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'understand the effect of the following operations on the dispersion of the data: (i) adding a common constant to each item of the set of data (ii) multiplying each item of the set of data by a common constant',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '理解下列情況對數據的離差之影響：(i)對數據的每一項加上一個相同的常數(ii)對數據的每一項乘以一個相同的常數',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 16,
                cn::LEARNING_OBJECTIVES_CODE_COL => '07'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '17.1',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'recognise different techniques in survey sampling and the basic principles of questionnaire design',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'recognise different techniques in survey sampling and the basic principles of questionnaire design',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '認識抽取調查樣本的不同技巧及製作問卷的基本原則',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 17,
                cn::LEARNING_OBJECTIVES_CODE_COL => '01'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '17.2',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'discuss and recognise the uses and abuses of statistical methods in various daily-life activities or investigations',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'discuss and recognise the uses and abuses of statistical methods in various daily-life activities or investigations',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '討論及認識各種日常活動或調查中統計方法的應用及誤用',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 17,
                cn::LEARNING_OBJECTIVES_CODE_COL => '02'
            ],
            [
                cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL => '17.3',
                cn::LEARNING_OBJECTIVES_TITLE_COL => 'assess statistical investigations presented in different sources such as news media, research reports, etc.',
                cn::LEARNING_OBJECTIVES_TITLE_EN_COL => 'assess statistical investigations presented in different sources such as news media, research reports, etc.',
                cn::LEARNING_OBJECTIVES_TITLE_CH_COL => '評估從新聞媒介、研究報告等不同來源所獲得的統計調查報告',
                cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL => 17,
                cn::LEARNING_OBJECTIVES_CODE_COL => '03'
            ]
        ];

        if(!empty($data)){
            foreach($data as $key => $value){
                // $checkExists = LearningsObjectives::where([cn::LEARNING_OBJECTIVES_TITLE_COL => $value[cn::LEARNING_OBJECTIVES_TITLE_COL]])->first();
                $checkExists = LearningsObjectives::IsAvailableQuestion()->where([cn::LEARNING_OBJECTIVES_TITLE_COL => $value[cn::LEARNING_OBJECTIVES_TITLE_COL]])->first();
                if(!isset($checkExists) && empty($checkExists)){
                    LearningsObjectives::create($value);
                }else{
                    LearningsObjectives::find($checkExists->id)->update($value);
                }
            }
        }
    }
}
