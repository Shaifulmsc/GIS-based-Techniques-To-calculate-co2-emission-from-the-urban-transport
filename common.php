<?php
if (isset($_REQUEST['upload_json'])) {
    upload_json_file();
}
if (isset($_REQUEST['car_type'])) {
    save_search_results();
}
function check_login()
{
    /* Check if user has been remembered */
    if (isset($_COOKIE['cookname'])) {
        $_SESSION['username'] = $_COOKIE['cookname'];
    }

    if (isset($_COOKIE['cookpass'])) {
        $_SESSION['user_pass'] = $_COOKIE['cookpass'];
    }

    if (isset($_COOKIE['cookrem'])) {
        $_SESSION['user_rem'] = $_COOKIE['cookrem'];
    }

    /* Username and password have been set */
    if (isset($_SESSION['username']) && isset($_SESSION['user_pass'])) {
        /* Confirm that username and password are valid */
        if (confirm_user($_SESSION['username'], $_SESSION['user_pass']) === FALSE) {
            /* Variables are incorrect, user not logged in */
            unset($_SESSION['username']);
            unset($_SESSION['user_pass']);
            unset($_SESSION['user_rem']);
            return FALSE;
        }
        $row = dbFetchAssoc(confirm_user($_SESSION['username'], $_SESSION['user_pass']));
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['last_login'] = $row['last_login'];
        return TRUE;
    } else {/* User not logged in */
        return FALSE;
    }
}

//check if user registered,his data(username,password) is correct ,if yes set  session and cookie
function user_login($username, $password)
{
    if (user_exists($username) == FALSE) { // check if user exists
        return "You are not a registered member";
    }
    else if (confirm_user($username, md5($password)) === FALSE) { // check if login data is correct
        return "Authentication error";
    }
    else {
        $_SESSION['username'] = $username;
        $_SESSION['user_pass'] = $password;
        $row = dbFetchAssoc(confirm_user($username, md5($password)));
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['last_login'] = $row['last_login'];
        // if choose remember_me save his login in the cookie
        if (isset($_POST['remember_me'])) {
            $_SESSION['user_rem'] = $_POST['remember_me'];
            setcookie("cookname", $_SESSION['username'], time() + 60 * 60 * 24 * COOKIE_TIME_OUT);
            setcookie("cookpass", $_SESSION['user_pass'], time() + 60 * 60 * 24 * COOKIE_TIME_OUT);
            setcookie("cookrem", $_SESSION['user_rem'], time() + 60 * 60 * 24 * COOKIE_TIME_OUT);
        } else {
            //destroy any previously set cookie
            setcookie("cookname", '', time() - 60 * 60 * 24 * COOKIE_TIME_OUT);
            setcookie("cookpass", '', time() - 60 * 60 * 24 * COOKIE_TIME_OUT);
            setcookie("cookrem", '', time() - 60 * 60 * 24 * COOKIE_TIME_OUT);
        }

        //Login history
        $sql = "UPDATE users  SET last_login= NOW()  WHERE username='" . $username . "'";

        dbQuery($sql);

        header('Location:' . WEB_ROOT . 'admin');
        exit;
    }
}

//do user logout
function user_logout()
{
    session_start();
    $_SESSION = array(); // reset session array
    session_destroy();   // destroy session.
    //delete from cookie if expires
    setcookie("cookname", '', time() - 60 * 60 * 24 * COOKIE_TIME_OUT);
    setcookie("cookpass", '', time() - 60 * 60 * 24 * COOKIE_TIME_OUT);
    setcookie("cookrem", '', time() - 60 * 60 * 24 * COOKIE_TIME_OUT);
    header('Location: ' . WEB_ROOT . 'admin/login.php');
    exit;
}

//check if user exist
function user_exists($username)
{
    $sql = "SELECT ua.username,ua.username,ua.last_login FROM users ua
            WHERE ua.username='$username'"
        . " LIMIT 1";

    $result = dbQuery($sql);

    if (!$result || (dbNumRows($result) < 1)) {
        return FALSE; //Indicates username failure
    }

    return $result;
}

//check if the username and password are correct
function confirm_user($username, $password)
{

    /* Verify that user is in database */
    $sql = "SELECT ua.username,ua.username,ua.last_login FROM users ua
            WHERE ua.username='$username'
                AND ua.password='$password' LIMIT 1";

    $result = dbQuery($sql);

    if (!$result || (dbNumRows($result) < 1)) {
        return FALSE; //Indicates username failure
    }

    return $result;
}

//check if carbon_results exist
function get_carbon_results()
{
    $sql = "select * from carbon_results order by id desc";

    $result = dbQuery($sql);

    if (!$result || (dbNumRows($result) < 1)) {
        return FALSE; //Indicates carbon_calculator failure
    }

    return $result;
}

//check if cars exist
function get_cars()
{
    $sql = "select * from cars";

    $result = dbQuery($sql);

    if (!$result || (dbNumRows($result) < 1)) {
        return FALSE; //Indicates cars failure
    }

    return $result;
}

function upload_json_file()
{
    #step one check  if the request POST has sent,
    #step two check  if there is a file with your restrictions and no error
    #step three read all data from file and convert it to array
    #step four add all lines to the current file
    #step five redirect to the index page

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $ext = pathinfo($_FILES["json_file"]["name"], PATHINFO_EXTENSION);

        if ($_FILES["json_file"]["error"] != 0) {
            $_SESSION['message'] = 'File has an issue.';
        } elseif ($ext !== 'json') {
            $_SESSION['message'] = "the extension is wrong";

        } elseif (($_FILES['json_file']['size'] >= 2097152) || ($_FILES["json_file"]["size"] == 0)) {
            $_SESSION['message'] = 'File too large. File must be less than 2 megabytes.';
        } else {
            copy($_FILES['json_file']['tmp_name'], 'uploads/' . $_FILES['json_file']['name']);
            $data = file_get_contents('uploads/' . $_FILES['json_file']['name']);
            import_json_data($data);
            $_SESSION['message'] = "File Uploaded Successfully";
        }
        header('Location: ' . 'admin');
    }
}

function import_json_data($data)
    {
        $cars = json_decode($data, true);

        $values = [];
        if (!empty($cars["results"]["bindings"])) {

            foreach ($cars["results"]["bindings"] as $car) {
                $short_name = $car["manufacturer_short_name"]["value"];
                $name = $car["manufacturer"]["value"];
                $release_year = $car["year"]["value"];
                $registration = $car["registration"]["value"];
                $av_mass_kg = $car["average_mass_kg"]["value"];
                $av_co2_km = $car["average_co2_emissions_g_km"]["value"];
                $values[] = "('$short_name','$name',$release_year,$registration,$av_mass_kg,$av_co2_km)";
            }
            $values = implode(", ", $values);
            $sql = "INSERT INTO cars(manufacturer_short_name,manufacturer,
                        release_year,registration,average_mass_kg,average_co2_km) 
                        VALUES {$values}" ;
            $db = mysqli_connect("localhost", "root", "", "demo") or die ("Failed to connect");
            $result = mysqli_query($db, $sql) or die(mysqli_error($db));
            if (!$result){
                $_SESSION['message'] = 'Json File Not Imported , it Has A wrong Data';
            }
        } else {
            $_SESSION['message'] = 'Json File Has A wrong structure or It is empty';
        }
    }

function save_search_results()
{
    if (isset($_REQUEST["car_type"]) && isset($_REQUEST["travel_mode"])
        && isset($_REQUEST["origin"]) && isset($_REQUEST["username"]) &
        isset($_REQUEST["destination"]) && isset($_REQUEST["distance_in_kilo"]) &
        isset($_REQUEST["distance_in_mile"]) && isset($_REQUEST["duration_text"]) &
        isset($_REQUEST["duration_value"]) && isset($_REQUEST["co2_result"])
    ) {

        $username = $_REQUEST["username"];
        $travel_mode = $_REQUEST["travel_mode"];
        $car_type = $_REQUEST["car_type"];
        $in_kilo = $_REQUEST["distance_in_kilo"];
        $in_mile = $_REQUEST["distance_in_mile"];
        $origin = $_REQUEST["origin"];
        $destination = $_REQUEST["destination"];
        $duration_text = $_REQUEST["duration_text"];
        $duration_value = $_REQUEST["duration_value"];
        $co2_result = $_REQUEST["co2_result"];
        $values = "('$username','$travel_mode','$car_type','$in_kilo','$in_mile','$origin',
                        '$destination','$duration_text','$duration_value','$co2_result')";
        $sql = "INSERT INTO carbon_results(
                        username,travel_model,car_type,distance_in_kilo,distance_in_mile,origin,
                        destination,duration_in_text,duration_in_minutes,co2_result) 
                        VALUES {$values}";

        $db = mysqli_connect("localhost", "root", "", "demo") or die ("Failed to connect");
        $result = mysqli_query($db, $sql) or die(mysqli_error($db));
        header('Content-Type: application/json');
        if ($result) {
            echo json_encode(array("status" => "1"));
        } else {
            echo json_encode(mysqli_error($db));
        }

    }else{
        echo json_encode("missing input");

    }
}
/*
 * End of common.php
 */