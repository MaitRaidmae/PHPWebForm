<!DOCTYPE html>
<!--
Licensed to BYS Inc.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Hello World</title>
        <link rel="stylesheet" href="index.css">
    </head>

    <body>
        <h1>Laenu soovi sisestus vorm</h1>
        <form action="index.php" method="post">
            <div id="name_div" class="form_field">
               <label for="name">Nimi:</label>
               <input type="text" id="name" name="name"  placeholder="Peeter Suur" value="<?php echo filter_input(INPUT_POST,'name',FILTER_SANITIZE_STRING)?>">
            </div>
            <div id="personal_code" class="form_field">
               <label for="name">Isikukood:</label>
               <input type="text" id="personal_code" name="personal_code"  placeholder="51107121760" value="<?php echo filter_input(INPUT_POST,'personal_code',FILTER_SANITIZE_NUMBER_INT)?>">
            </div>
            <div id="amount_div" class="form_field">
                <label for="name">Laenu summa:</label>
                <input type="text" id="amount" name="amount" placeholder="1000 kuni 10000" value="<?php echo filter_input(INPUT_POST,'amount',FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION)?>">
            </div>
            <div id="period_div" class="form_field">
                <label for="name">Periood kuudes:</label>
                <input type="text" id="period" name="period" placeholder="6 kuni 24" value="<?php echo filter_input(INPUT_POST,'period',FILTER_SANITIZE_NUMBER_INT)?>">
            </div>
            <div id="purpose_div" class="form_field">
                <label for="name">Kasutuseesmärk:</label>
                <input type="text" id="purpose" name="purpose" placeholder="näiteks: puhkus" value="<?php echo filter_input(INPUT_POST,'purpose',FILTER_SANITIZE_STRING)?>">
            </div>
            <input type="submit" value="Saada" class="btnSubmit">
        </form>
        <?php
        include 'loanForm.php';
        if (null != filter_input_array(INPUT_POST)) {
            echo '<div class="errors">';
            $loanForm = new loanForm(filter_input_array(INPUT_POST));
            $errors = $loanForm->getErrors();
            if (count($errors) == 0) {
                $loanForm->printToFile();
                header("Location: submit_success.php");
            } else {
                foreach ($errors as $error) {
                    echo "<p>" . $error . "</p>";
                }
            }    
            echo "</div>";
        } ?>
    </body>
</html>
