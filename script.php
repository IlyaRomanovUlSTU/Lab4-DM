<?php

$matrix;
$start;
$finish;
$minLength = 0;
$shortestPath = array();

/**
    * Функция для валидации введённой матрицы
    *
    * Функция удаляет пустые элементы посредством метода array_diff() и пустые строки посредством метода unset(), осуществляет проверку размера матрицы, наличия элементов, не являющихся целыми числами, и равенства элементов на главной диагонали нулю
    *
    * @param matrix - введённая пользователем матрица    
*/

function validateMatrix(& $matrix)
{
    for ($i = 0; $i < count($matrix); $i++)
    {
        $matrix[$i] = array_diff($matrix[$i], array(""));
        if (count($matrix[$i]) == 0)
        {
            unset($matrix[$i]);
            $matrix = array_values($matrix);
            $i--;
        }
    }    
    if (count($matrix) == 0)
    {
        echo "Пропущено поле ввода!";
        exit;
    }
    
    for ($i = 0; $i < count($matrix); $i++)
    {
        if (count($matrix[$i]) != count($matrix))
        {
            echo "Введенная матрица не квадратная!";
            exit;
        }
    }
    for ($i = 0; $i < count($matrix); $i++)
    {
        for ($j = 0; $j < count($matrix[$i]); $j++)
        {
            if ($matrix[$i][$j] != "-")
            {
                for ($k = 0; $k < mb_strlen($matrix[$i][$j]); $k++)
                {
                    if ($matrix[$i][$j][$k] < "0" || $matrix[$i][$j][$k] > "9")
                    {
                        echo "Неправильный формат данных!";
                        exit;
                    }
                }
                $matrix[$i][$j] = (int)$matrix[$i][$j];
            }
        }
    }
    for ($i = 0; $i < count($matrix); $i++)
    {
        for ($j = 0; $j < count($matrix[$i]); $j++)
        {
            if ($i == $j)
            {
                if ($matrix[$i][$j] !== 0)
                {
                    echo "Элемент, находящийся на главной диагонали, не равен нулю!";
                    exit;
                }
            }
            else
            {
                if ($matrix[$i][$j] === 0)
                {
                    echo "Элемент, не находящийся на главной диагонали, равен нулю!";
                    exit;
                }
            }
        }
    }
}

/**
    * Функция для валидации введённых номеров начальной и конечной точки маршрута
    *
    * Функция осуществляет проверку формата входных данных и соответствия номера точки размеру весовой матрицы
    *
    * @param point - введённый пользователем номер точки, matrixSize - размер весовой матрицы графа
*/

function validatePoint(& $point, $matrixSize)
{
    if ($point == "")
    {
        echo "Пропущено поле ввода!";
        exit;
    }
    
    for ($i = 0; $i < mb_strlen($point); $i++)
    {
        if ($point[$i] < "0" || $point[$i] > "9")
        {
            echo "Неправильный формат данных!";
            exit;
        }
    }
    
    $point = (int)$point - 1;   
    
    if ($point < 0 || $point >= $matrixSize)
    {
        echo "Вершина не найдена!";
        exit;
    }    
}

/**
    * Функция для нахождения кратчайшего пути между заданными вершинами
    *
    * Функция осуществляет поиск кратчайшего пути между заданными вершинами посредством рекурсии, определяя длину кратчайшего пути и записывая пройденный маршрут
    *
    * Метод in_array() позволяет избежать прохождения по циклическим участкам графа
    *
    * @param currentPosition - номер вершины, проверяемой на текущем шаге, length - расстояние от начальной точки до текущей вершины, path - маршрут, пройденный от начальной точки до текущей вершины
*/

function findShortestPath($currentPosition, $length, $path)
{
    global $finish;
    global $minLength;    
    
    if ($currentPosition == $finish)
    {      
        if ($minLength == 0 || $length < $minLength)
        {
            global $shortestPath;
            
            $minLength = $length;
            if ($length == 0)
            {
                $path[] = $currentPosition;
            }
            $shortestPath = $path;
        }        
    }
    else
    {
        global $matrix;

        for ($i = 0; $i < count($matrix[$currentPosition]); $i++)
        {
            if ($matrix[$currentPosition][$i] !== 0 && $matrix[$currentPosition][$i] !== "-")
            {
                if ($minLength == 0 || $length + $matrix[$currentPosition][$i] < $minLength)
                {
                    if (!in_array($i, $path))
                    {
                        $path[] = $i;
                        findShortestPath($i, $length+ $matrix[$currentPosition][$i], $path);
                    }                    
                }
            }
        }
    }
}

/**
    *Основное тело программы
	*	
	*Здесь вызываются все необходимые программе функции
    *
    *Метод explode() позволяет разделять массив по пробелам или другим знакам
*/

$matrix = explode(PHP_EOL, $_POST["matrix"]);
for ($i = 0; $i < count($matrix); $i++)
{
    $matrix[$i] = explode(" ", $matrix[$i]);
    
}

$start = $_POST["start"];
$finish = $_POST["finish"];

validateMatrix($matrix);
validatePoint($start, count($matrix));
validatePoint($finish, count($matrix));

findShortestPath($start, 0, $path = array($start));

if ($minLength == 0 && $start != $finish)
{
    echo "Путь из вершины " . ($start + 1) . " в вершину " . ($finish + 1) . " не найден.";
}
else
{
    echo "Длина кратчайшего пути из вершины " . ($start + 1) . " в вершину " . ($finish + 1) . " равна " . $minLength . "<br>";
    echo "Кратчайший маршрут ";
    for ($i = 0; $i < count($shortestPath) - 1; $i++)
    {
        echo ($shortestPath[$i] + 1) . " => "; 
    }
    echo $shortestPath[count($shortestPath) - 1] + 1;
}

?>