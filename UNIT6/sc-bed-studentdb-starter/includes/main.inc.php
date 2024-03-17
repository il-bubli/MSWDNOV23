<?php

session_start();

/**
 * Load a value from the session or if not found from a cookie
 * @param string $key the name of the value to load.
 * @param bool $json of true, the loaded value should be decoded from JSON
 * @return mixed the requested value of null if not found
 */
function loadValue(string $key, bool $json = false): mixed
{
    $result = null;
    if (isset($_SESSION[$key])) {
        $result = $_SESSION[$key];
    } elseif (isset($_COOKIE[$key])) {
        $result = $json ? json_decode($_COOKIE[$key]) : $_COOKIE[$key];
        $_session[$key] = $result;
    }
    return $result;
}

/**
 * Saves a value to the session and to a cookie
 * @param string $key the name to save the value with
 * @param mixed $value the value to save
 * @param bool $json whether to JSON-encode the value
 * @param int $exp the expiry time for the cookie
 * @return void
 */
function saveValue(string $key, mixed $value, bool $json = false, int $exp = 2628288) : void
{
    $_SESSION[$key] = $value;
    setcookie($key, $json ? json_encode($value) : $value, time() + $exp,'/');
}

// Initialise the student list
$studentList = loadValue('studentList', true) ?? [];
//Initialise the sorting column
$sortBy = loadValue('sortBy');

sortStudents();

// Determine the action
$action = null;
if (filter_var($_SERVER['REQUEST_METHOD']) === 'POST') {
    $action = filter_input(INPUT_POST,'action');
} elseif (filter_var($_SERVER['REQUEST_METHOD']) === 'GET') {
    $action = filter_input(INPUT_GET,'action');
}


switch($action) {
    case 'add':
        addStudent();
        break;
    case 'delete':
        deleteStudent();
        break;
    case 'sort':
        sortStudents();
        break;
}

function addStudent()
{
    global $studentList;
    $studentList[] = [
        'name' => filter_input(INPUT_POST,'student_name'),
        'surname'=> filter_input(INPUT_POST,'student_surname'),
        'grade'=> filter_input(INPUT_POST,'student_grade')
    ];
    saveValue('studentList', $studentList, true);
    sortStudents();
}

function deleteStudent()
{
    global $studentList;
    $key = filter_input(INPUT_GET,'studId', FILTER_SANITIZE_NUMBER_INT);
    unset($studentList[$key]);
    saveValue('studentList', $studentList, true);
    sortStudents();
}
function sortStudents()
{
    global $studentList, $sortBy;
    if (filter_input(INPUT_GET, 'sortBy', FILTER_DEFAULT, FILTER_NULL_ON_FAILURE)) {
        //new sort
        $sortBy = filter_input(INPUT_GET,'sortBy');
        saveValue('sortBy', $sortBy);
    } else {
        // previous sort
        $sortBy = loadValue('sortBy');
    }

    if ($sortBy === 'id' || empty($sortBy)) {
        ksort($studentList);
    } else {
        uasort($studentList, fn($a, $b) => $a[$sortBy] <=> $b[$sortBy]);
    }
}
?>


<div class="grid gap-0 row-gap-3">
    <div class="p-2 g-col-12">
        <div class="card">
            <div class="card-body">
                <h1><span class="logo"><i class="bi bi-people-fill"></i></span></a> StudentDB</h1>
            </div>
        </div>
    </div>
    <div class="p-2 g-col-12">
        <div class="card">
            <div class="card-body">
                <form name="convertForm" id="convertForm" method="POST">
                    <div class="row mb-3 gy-2">
                        <div class="col-sm-12 col-md-6">
                            <input name="student_name" id="student_name" type="text"
                                class="form-control form-control-lg" placeholder="Name" required>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <input name="student_surname" id="student_surname" type="text"
                                class="form-control form-control-lg" placeholder="Surname" required>
                        </div>
                    </div>
                    <div class="row mb-3 gy-2">
                        <div class="col-sm-12 col-md-6">
                            <input name="student_grade" id="student_grade" type="number" step="any" min="0" max="100"
                                class="form-control form-control-lg" placeholder="Grade" required>
                        </div>
                    </div>
                    <div class="row mb-3 gy-2">
                        <div class="col-sm-12 col-md-6">
                            <input type="hidden" name="action" id="action" value="add">
                            <input class="btn btn-primary btn-lg" type="submit" value="Add">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php if (count($studentList) > 0) { ?>
        <div class="p-2 g-col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><a href="index.php?action=sort&sortBy=id">ID</a></th>
                                <th><a href="index.php?action=sort&sortBy=name">Name</a></th>
                                <th><a href="index.php?action=sort&sortBy=surname">Surname</a></th>
                                <th><a href="index.php?action=sort&sortBy=grade">Grade</a></th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // TODO Show results here
                            foreach($studentList as $key => $student) {
                                printf(
                                    '<tr><td>%3d</td><td>%s</td><td>%s</td><td>%d</td><td>%s</td></tr>',
                                    $key,
                                    $student['name'],
                                    $student['surname'],
                                    $student['grade'],
                                    "<a href='index.php?action=delete&studId=$key'><i class='bi bi-trash3-fill'></i></a>"
                                );                                
                            }
                            ?>
                        </tbody>
                </div>
            </div>
        </div>
    <?php } ?>
    
</div>