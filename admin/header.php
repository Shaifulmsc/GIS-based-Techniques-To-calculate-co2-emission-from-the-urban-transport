<?php
require_once '../config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
    <script>
        $(document).ready( function () {
            $('#cars_table').DataTable({});
        } );
    </script>
</head>
<?php include ('footer.php'); ?>
<body>
<div class="container">

</body>
</html>