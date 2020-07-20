<?php
require_once 'header.php';
if(empty($_SESSION['username'])){
    header('Location:' . WEB_ROOT . 'admin/login.php');
}
?>

    <div class="jumbotron">
        <h1>Cars List</h1>
        <a href="<?php echo WEB_ROOT; ?>admin/logout.php" class="btn btn-default"  onclick="return confirm('Are you sure want to logout?')">Logout</a>
        <a class="btn btn-default" href="<?php echo WEB_ROOT; ?>admin/carbon_results.php">Carbon results</a>
    </div>

    <div class="row">
        <h3> Import Json File</h3>
        <?= isset($_SESSION['message']) ? $_SESSION['message'] : ""; ?>
        <form action="../common.php" method="post" enctype="multipart/form-data">
            <div class="input-group">
                <input type="file" name="json_file" class="form-control">
                <span class="input-group-btn">
                <button class="btn btn-default" type="submit" name="upload_json" value="1">Upload!</button>
                </span>
            </div>
        </form>
        <hr>
        <table id="cars_table" class="table table-striped table-bordered" style="width:100%">
            <thead>
            <tr>
                <th>#</th>
                <th>Manufacturer Short Name</th>
                <th>Manufacturer</th>
                <th>Release Year</th>
                <th>Registration</th>
                <th>Average Mass KG</th>
                <th>Average CO2 Per KM</th>
                <th>Created at</th>
            </tr>
            </thead>
            <?php
            $result = get_cars();
            if(!empty($result)){
            $cars = resultToArray($result);
            foreach ($cars as $key => $car) { ?>
                <tr>
                    <td><?= $car['id'] ?></td>
                    <td><?= $car['manufacturer_short_name'] ?></td>
                    <td><?= $car['manufacturer'] ?></td>
                    <td><?= $car['release_year'] ?></td>
                    <td><?= $car['registration'] ?></td>
                    <td><?= $car['average_mass_kg'] ?></td>
                    <td><?= $car['average_co2_km'] ?></td>
                    <td><?= $car['created_at'] ?></td>

                </tr>

            <?php } }else{
                echo "<div class=\"alert alert-danger\">
                    <strong>Sorry!</strong> There is No Data Yet , You Should Import Json Car File.
                     </div>" ;
            } ?>

        </table>
