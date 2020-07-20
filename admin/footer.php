<style>
    /* Add some padding on document's body to prevent the content
    to go underneath the header and footer */
    body {
        padding-top: 60px;
        padding-bottom: 40px;
        background: #EDF5ED;
    }

    .container {
        width: 90%;
        margin: 0 auto; /* Center the DIV horizontally */
    }

    .fixed-header, .fixed-footer {
        width: 100%;
        position: fixed;
        background: #333;
        padding: 5px 0;
        color: #0edf40;
        z-index: 50;
    }

    .fixed-header {
        top: 0;
    }

    .fixed-footer {
        bottom: 0;
    }

    /* Some more styles to beutify this example */
    nav a {
        color: #0edf40;
        text-decoration: none;
        padding: 4px 15px;
        display: inline-block;
    }

    .container p {
        line-height: 100px; /* Create scrollbar to test positioning */
    }
</style>
<div class="fixed-header">
    <div class="container" align="center">
        <nav>
            <a href="index.php"><?= $lang['home'] ?></a>
            <a href="admin/about.php"><?= $lang['about'] ?></a>
            <a href="#"><?= $lang['products'] ?></a>
            <a href="#"><?= $lang['services'] ?></a>
            <a href="admin/login.php"><?= $lang['contact_us'] ?></a>


        </nav>
    </div>
    <h2 align="center"><?= $lang['menu_message'] ?></h2>

</div>

<div class="fixed-footer">

    <div class="container" align="center"> @Copyright:2019-2020 hska-issh1011</div>
</div>
