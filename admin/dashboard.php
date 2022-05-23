<?php
include 'validate_credentials.php';

// $host = $_SERVER["HTTP_HOST"];
// echo "<br><br>";
// echo $host;
// echo "<br><br>";
// echo $_SERVER["DOCUMENT_ROOT"];
// echo "<br><br>";
// echo __DIR__;
// echo "<br><br>";
// echo dirname(__FILE__,2);
// echo "<br><br>";
// $padre = dirname(__DIR__, 4);
// echo $padre;
?>

    <div class="wrap">

        <div class="border bg-light text-dark" style="padding: 10px 20px 15px;">
            <?php
            echo '<h1 class="wp-heading-inline">' . get_admin_page_title() . '</h1>';
            ?>           
        </div>


        <br>
        <div class="container">
            <div class="row">
                <div class="col-md-12 border">
                    <div id="chart_div"></div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-6 border">
                    <div id="chart_div_2" style="width: 100%; height: 500px;"></div>

                </div>
                <div class="col-md-6 border">
                    <div id="donutchart" style="width: 900px; height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>

