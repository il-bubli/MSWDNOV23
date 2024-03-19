<?php
namespace com\icemalta\libpayroll;

trait PayslipMetaData
{
    public const CURRENCY = '€';

    public function getPayslipDetails(): array
    {
        return [
            'currency' => self::CURRENCY,
            'content' => $this->getPayslip()
        ];
    }
}

abstract class Worker 
{
    use PayslipMetaData;

    private string $name;
    private string $surname;

    public function __construct(string $name, string $surname) 
    {
        $this->name = $name;
        $this->surname = $surname;
    }

    abstract public function getRoleDescription(): string;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name) : void 
    {
        $this->name = $name;
    }

    public function getSurname(): string 
    {
        return $this->surname;
    }

    public function setSurname(string $surname) : void
    {
        $this->surname = $surname;
    }
}

interface StaffMember
{
    public function getPerksString(): string;
    public function getHealthBenefitsString(): string;
}

class Manager extends Worker implements StaffMember
{
    public function getPaySlip(): string
    {
        return "This is the payslip for the manager.";
    }

    public function getRoleDescription(): string 
    {
        return "This is the description for the manager";
    }

    public function getPerksString(): string
    {
        return 'Retirement contribution, 24/7 Gym, Meals, Business-Class Travel.';
    }

    public function getHealthBenefitsString(): string
    {
        return 'Full private hospital, dental, cosmetic.';
    }
}

class Employee extends Worker implements StaffMember
{
    public function getPaySlip(): string
    {
        return "This is the payslip for the Employee.";
    }

    public function getRoleDescription(): string 
    {
        return "This is the description for the employee";
    }

    public function getPerksString(): string
    {
        return 'Retirement contribution, 24/7 Gym, Meals.';
    }

    public function getHealthBenefitsString(): string
    {
        return 'Full private hospital, dental.';
    }
}

class Contractor extends Worker
{
    public function getPaySlip(): string
    {
        return "This is the payslip for the contractor.";
    }

    public function getRoleDescription(): string 
    {
        return "This is the description for the contractor";
    }
}