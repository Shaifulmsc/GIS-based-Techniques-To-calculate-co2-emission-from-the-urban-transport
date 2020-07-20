<?php
require 'header.php';
?>

<div class="jumbotron">
        <h1>Carbon results List</h1>
    <a href="<?php echo WEB_ROOT; ?>admin/logout.php" class="btn btn-default"  onclick="return confirm('Are you sure want to logout?')">Logout</a>
    <a class="btn btn-default" href="<?php echo WEB_ROOT; ?>admin">Cars List</a>
    </div>
    <div class="row">
        <?= isset($_SESSION['message']) ? $_SESSION['message'] : ""; ?>
        <table id="cars_table" class="table table-striped table-bordered" style="width:100%">
            <thead>
            <tr>
                <th>#</th>
                <th>username</th>
                <th>TravelMode</th>
                <th>Car Type</th>
                <th>distance_in_kilo</th>
                <th>distance_in_mile</th>
                <th>duration_in_text</th>
                <th>duration_in_minutes</th>
                <th>origin</th>
                <th>destination</th>
                <th>co2_result</th>
                <th>Created at</th>
            </tr>
            </thead>
            <?php
            $result = get_carbon_results();
            if(!empty($result)){
            $cars = resultToArray($result);
            foreach ($cars as $key => $car) { ?>
                <tr>
                    <td><?= $car['id'] ?></td>
                    <td><?= $car['username'] ?></td>
                    <td><?= $car['travel_model'] ?></td>
                    <td><?= $car['car_type'] ?></td>
                    <td><?= $car['distance_in_kilo'] ?></td>
                    <td><?= $car['distance_in_mile'] ?></td>
                    <td><?= $car['duration_in_text'] ?></td>
                    <td><?= $car['duration_in_minutes'] ?></td>
                    <td><?= $car['origin'] ?></td>
                    <td><?= $car['destination'] ?></td>
                    <td><?= $car['co2_result'] ?></td>
                    <td><?= $car['created_at'] ?></td>

                </tr>

            <?php } }else{
                echo "<div class=\"alert alert-danger\">
                    <strong>Sorry!</strong> There is No Data Yet , You Should Import Json Car File.
                     </div>" ;
            } ?>

        </table>
<script>
    function logout() {
        var r = confirm('Are you sure want to logout?');
        if (r === true) {
            console.log("aaaaaaa");
            window.location.href='cancel';
        } else {
            window.location.href='index.php'
        }
    }
<?php
require 'footer.php';
?>