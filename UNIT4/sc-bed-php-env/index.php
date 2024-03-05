<?php
function add($num1, $num2)
{
    return $num1 + $num2;
}

$result = add(5, 7);
echo "<p>The result is $result.</p>";

// Passing values to a function by value

function add10($num)
{
    $num +=10;
}
$a = 4;
echo "<p>Value before function call: $a</p>";
add10($a);
echo "<p>Value after function call: $a</p>";

// Passing values to a function by reference
function add20(&$num)
{
    $num += 20;
}
$b = 4;
echo "<p>Value before function call: $b</p>";
add20($b);
echo "<p>Valueafter function call: $b</p>";

// Default parameters in functions
function raiseToPower($num, $power = 2)
{
    return pow($num, $power);
}
printf('<p>With power specified: %d</p>', raiseToPower(10,5));
printf('<p>Without power specified: %d</p>', raiseToPower(10));

//Accepting multiple paramters
function sum()
{
    $total =0;
    foreach(func_get_args()as $num)
    {
        $total += $num;
    }
    return $total;
}
printf('<p>Adding numbers together: %d</p>', sum(10, 87, 42, 56));

// Variadics
function sum2(...$nums)
{
    $total =0;
    foreach($nums as $num)
    {
        $total += $num;
    }
    return $total;
}
printf('<p>Adding numbers together: %d</p>', sum2(10, 87, 42, 56));

// Returning Values from a function
$garage = ['Kia', 'BMW', 'Tesla', 'Ford', 'GM'];

//Returning a result by value (default)
function getVehicle($carNumber = 0)
{
    global $garage;
    return $garage[$carNumber];
}

echo "<p>Array before changes:</p>";
print_r($garage);
$car = getVehicle(1);
$car = "Mercedes";
echo "<p>Array after changes:</p>";
print_r($garage);

// Returning a result by reference
function &getVehicle2($carNumber = 0)
{
    global $garage;
    return $garage[$carNumber];
}

echo "<p>Array before changes:</p>";
print_r($garage);
$car = &getVehicle2(1);
$car = "Mercedes";
echo "<p>Array after changes:</p>";
print_r($garage);

// Nested Functions
function showPrices(...$prices)
{
    function showAsEuro($value)
    {
        return '€' . number_format($value, 2, '.', ',');
    }
    echo '<ul>';
    foreach ($prices as $price){
        echo '<li>' . showAsEuro($price) . '</li>';
    }
    echo '</ul>';
}
showPrices(34.789, 212.009, 1612.881, 12.89);

// ADVANCED STUFF

// Variable Functions
function myFunc()
{
    echo "<p>Hello! This is myFunc()</p>";
}
//myFunc();
$myVar = 'myFunc';
$myVar();

// Anonymous Functions
$greeting = function($name){
    return "Hello, $name";
};
printf('<p>%s</p>', $greeting('Bob'));

// Fat-arrow syntax
$anotherGreeting = fn($name) => "Hello again, $name";
printf("<p>%s</p>", $anotherGreeting("Bob"));

// Example: Using a fat arrow function as a callback
$productPrices = [34.789, 212.009, 1612.881, 12.89];
$result = array_map(fn($price) => '€' . number_format($price, 2, '.', ','), $productPrices);
print_r($result);

// Fat-arrow functions can only have 1 line of code, so:
$freeThreshold = 40;
$processedCart = array_map(
    function ($price) use ($freeThreshold) {
        $delivery = '';
        if ($price > $freeThreshold) {
            $delivery = 'Free delivery!';
        }
        return '€' . number_format($price, 2,'.', ',') . " $delivery";
    },
    $productPrices
);
print_r($processedCart);

// Type Hinting
function showCartTotal(array $cartItems, string $currency, bool $rightPlacement, float $freeThreshold): string
{
    $cartTotal = array_sum($cartItems);
    $formattedTotal = number_format($cartTotal, 2, ".",",");
    $result = '<p>Your total is <strong>' . ($rightPlacement ? $formattedTotal . $currency : $currency . $formattedTotal) . '</strong></p>';
    if($cartTotal >= $freeThreshold) {
        $result .= '<p>Your cart qualifies for free delivery.</p>';
    }
    return $result;
}
echo showCartTotal([23, 45, 67], '€', false, 40);

// Static variables
function sayHello($user)
{
    static $callCount = 0;
    $callCount++;
    echo "<p>Hello, $user. Thefunction has been called $callCount times!";
}
sayHello("Bob");
sayHello('Alice');
sayHello('Terence');