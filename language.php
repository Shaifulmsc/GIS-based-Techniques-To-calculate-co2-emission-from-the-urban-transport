<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Calculating Carbon Dioxide Emission</title>
</head>
<body>
  <!-- Language -->
  <script>
            function changeLang() {
                document.getElementById('form_lang').submit();
            }
        </script>
        <form method='get' action='' id='form_lang'>
            <div class="form-group">
            Select Language : <select class="form-control" name='lang' onchange='changeLang();'>
                <option value='en' <?php if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'en') {
                    echo "selected";
                } ?> >English
                </option>
                <option value='fr' <?php if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'fr') {
                    echo "selected";
                } ?> >French
                </option>
                <option value='de' <?php if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'de') {
                    echo "selected";
                } ?> >Germany
                </option>
            </select>
            </div>
        </form>
</body>
</html>     