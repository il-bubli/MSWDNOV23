<?php
namespace unit7;
require 'lib/com/icemalta/libpayroll/payroll.php';

use com\icemalta\libpayroll\{
    Employee as PayrollEmployee,
    Manager as PayrollManager,
    Contractor as PayrollContractor,
    Worker as PayrollWorker,
    StaffMember
}
    ;
class Employee
{
    // Class constants
    public const CURRENCY = '€';

    public const TAX_RATE = 'Individual';

    private const YEAR_HOURS_STANDARD = 1920;
    private const TAX_BRACKETS = [
        ['from' => 0, 'to' => 9100, 'rate' => 0, 'subtract' => 0],
        ['from'=> 9101, 'to'=> 14500, 'rate' => 15, 'subtract' => 1365],
        ['from'=> 14501, 'to'=> 19500, 'rate' => 25, 'subtract' => 2815],
        ['from'=> 19501, 'to'=> 60000, 'rate' => 25, 'subtract' => 2725],
        ['from'=> 60001, 'to'=> null, 'rate' => 35, 'subtract' => 8725]
    ];

    // Static member
    private static array $employeeList = [];


    private string $name;
    private string $surname;
    private string $jobTitle;
    protected float $hourlyRate;
    private bool $paidOvertime = false;

    public function __construct(string $name, string $surname, string $jobTitle,float $hourlyRate = 0, bool $paidOvertime = false)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->jobTitle = $jobTitle;
        $this->hourlyRate = $hourlyRate;
        $this->paidOvertime = $paidOvertime;
        self::$employeeList[] = $this;
    }

    //Getters and setters

    public static function getEmployees(): array
    {
        return self::$employeeList;
    }
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void 
    {
        $this->name = $name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): void 
    {
        $this->surname = $surname;
    }

    public function getJobTitle(): string
    {
        return $this->name;
    }

    public function setJobTitle(string $jobTitle): void 
    {
        $this->jobTitle = $jobTitle;
    }

    public function getHourlyRate(): float
    {
        return $this->hourlyRate;
    }

    public function setHourlyRate(float $hourlyRate): void
    {
        if (is_numeric($hourlyRate) && $hourlyRate > 0) {
            $this->hourlyRate = $hourlyRate;
        }
    }

    public function getBasicDetailsString(): string
    {
        return "$this->name $this->surname,$this->jobTitle";
    }

    public function getGrossPay(float $hoursWorked): float
    {
        return $hoursWorked * $this->hourlyRate;
    }

       /**
     * @param float $hoursWorked the total hours worked by this employee this year
     * @return object an object containing calculated fields for income statement
     */
    public function getIncomeStatement(float $hoursWorked): object
    {
        $standardHours = $hoursWorked >= self::YEAR_HOURS_STANDARD ? self::YEAR_HOURS_STANDARD : $hoursWorked;
        $overtimeHours = $hoursWorked > self::YEAR_HOURS_STANDARD ? $hoursWorked - self::YEAR_HOURS_STANDARD : 0;

        $wageDetail = new \stdClass();
        $wageDetail->standardHours = $standardHours;
        $wageDetail->overtimeHours = $this->paidOvertime ? $overtimeHours : 0;
        $wageDetail->standardGross = $standardHours * $this->hourlyRate;
        $wageDetail->standardGross += $this instanceof TeamLead ? $this->bonusEntitlement : 0;
        $wageDetail->overtimeGross = $this->paidOvertime ? $overtimeHours * $this->hourlyRate * 1.5 : 0;
        $wageDetail->totalGross = $wageDetail->standardGross + $wageDetail->overtimeGross;
        $wageDetail->totalTax = $this->getTaxAmount($wageDetail->totalGross);
        $wageDetail->totalNet = $wageDetail->totalGross - $wageDetail->totalTax;

        return $wageDetail;
    }

    /**
     * @param float $grossWage amount to calculate tax for
     * @return float the total tax to be paid, or null if wage given is invalid (ex: negative wage)
     */
    private function getTaxAmount(float $grossWage): float|null
    {
        foreach (self::TAX_BRACKETS as $taxBracket) {
            if ($grossWage >= $taxBracket['from'] && ($taxBracket['to'] === null || $grossWage <= $taxBracket['to'])) {
                return ($grossWage - $taxBracket['subtract']) * $taxBracket['rate'] / 100;
            }
        }
        return null;
    }
}

class TeamLead extends Employee
{
    public function __construct(string $name, string $surname, string $jobTitle, float $hourlyRate = 0, bool $paidOvertime = false, float $bonusEntitlement = 0)
    {
        parent::__construct($name, $surname, $jobTitle, $hourlyRate, $paidOvertime);
        $this->bonusEntitlement = $bonusEntitlement;
    }
    protected float $bonusEntitlement = 0;

    public function getBonusEntitlement():float {
        return $this->bonusEntitlement;
    }

    public function setBonusEntitlement(float $bonusEntitlement): void
    {
        $this->bonusEntitlement = $bonusEntitlement;
    }

    public function getGrossPay(float $hoursWorked): float
    {
        return ($hoursWorked * $this->hourlyRate) + $this->bonusEntitlement;
    }
}

// Crearting an object (instance)
$emp1 = new Employee('Alice', 'Anerson', 'Head of Technology');

$emp2 = new Employee('Bob', 'Barker', 'Chief Marketing Officer');

$emp3 = new Employee('Claire', 'Curmi', 'Junior Programmer', 30);

$emp4 = new TeamLead('Dave','Dimech', 'Lead Programmer', 30, true, 4000);

$divResult = null;

if (filter_var($_SERVER['REQUEST_METHOD'], FILTER_DEFAULT) === 'POST') {
    $action = filter_input(INPUT_POST, 'action', FILTER_DEFAULT);
    switch ($action) {
        case 'divide': 
            global $divResult;
            $num1 = filter_input(INPUT_POST, 'num1', FILTER_DEFAULT);
            $num2 = filter_input(INPUT_POST, 'num2', FILTER_DEFAULT);
            try {
                $divResult = simpleDivision($num1, $num2);
            } catch (\TypeError $e) {
                $divResult = 'Make sure both num1 and num2 are numbers!';
                error_log('An error occured in index.php on line 184.');
            } catch (\DivisionByZeroError $e) {
                $divResult = 'You cannot divide by zero!';
            }
            break;
    };
}

function simpleDivision(float $num1, float $num2) : float {
    return $num1 / $num2;
}
?>

<!doctype html>
<html lang="en">
    <head>
        <title>OOP in PHP</title>
    </head>
    <body>
        <h1>OOP</h1>
        <p>
            <?= $emp1->getBasicDetailsString() ?>
        </p>
        <p>
            <?= $emp2->getBasicDetailsString() ?>
        </p>
        <p>
            Name: <?= $emp3->getBasicDetailsString() ?><br>
            Wage: <?= $emp3->getGrossPay(160) ?>
        </p>
        <?php
        printf('<h3>Income Statement for %s %s (%s)</h3>', $emp4->getName(), $emp4->getSurname(), $emp4->getJobTitle());
        $payslip = $emp4->getIncomeStatement(2000);
        $c = Employee::CURRENCY;
        echo <<<DETAILS
                <table border='1'>
                    <thead><tr><th>Item</th><th>Value</th></tr></thead>
                    <tbody>
                        <tr><td>Hours Worked</td><td>$payslip->standardHours</td></tr>
                        <tr><td>Overtime Worked</td><td>$payslip->overtimeHours</td></tr>
                        <tr><td>Standard Gross</td><td>$c$payslip->standardGross</td></tr>
                        <tr><td>Overtime Gross</td><td>$c$payslip->overtimeGross</td></tr>
                        <tr><td>Gross Wage</td><td>$c$payslip->totalGross</td></tr>
                        <tr><td>Tax Due</td><td>$c$payslip->totalTax</td></tr>
                        <tr><td>Net Wage</td><td>$c$payslip->totalNet</td></tr>
                    </tbody>
                </table>
            DETAILS;
        ?>

        <p>
            Name: <?= $emp4->getBasicDetailsString() ?><br>
            Wage: <?= $emp4->getGrossPay(1920) ?>
        </p>

        <h3>List of Employees</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Job Title</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach (Employee::getEmployees() as $employee) {
                    printf(
                        '<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
                        $employee->getName(),
                        $employee->getSurname(),
                        $employee->getJobTitle(),
                        $employee instanceof TeamLead ? 'Team Lead' : 'Employee'
                    );
                }
                ?>
            </tbody>
        </table>

        <h3>Using the Payroll Library</h3>
        <?php
            $joe = new PayrollEmployee('Joe', 'Doe');
            echo "<p>{$joe->getPaySlip()}</p>";

            $humans = [
                new PayrollEmployee('Joseph', 'Galea'),
                new PayrollManager('Gail', 'Vassallo'),
                new PayrollContractor('Nikolai', 'Sammut')
            ];

            foreach($humans as $human) {
                echo describeHuman($human);
            }

            function describeHuman(PayrollWorker $human): string
            {
                $result = sprintf('<p>%s %s (%s)</p>', $human->getName(), $human->getSurname(), $human->getRoleDescription());
                if ($human instanceof StaffMember) {
                    $result .= "<p>Health: {$human->getHealthBenefitsString()}</p>";
                    $result .= "<p>Perks: {$human->getPerksString()}</p>";
                }
                $ps = $human->getPayslipDetails();
                $result .= "<p>Payslip ({$ps['currency']}): {$ps['content']}</p>";
                return $result;
            }
        ?>

        <h3>Simple Division</h3>
        <?php
            if (isset($divResult)) {
                echo $divResult;
            }
        ?>
        <form name="divideForm" method="POST">
            <input type="hidden" name="action" value="divide">
            <label for="num1">Num 1</label>
            <input type="text" name="num1">
            <label for="num2">Num 2</label>
            <input type="text" name="num2">
            <input type="Submit" value="Divide">
        </form>
    </body>

</html>