<?php
$sp_radio = "checked";
$na_radio = "";
$args = array(
    'post_type' => 'member',
    'posts_per_page' => -1,
    'numberposts' => -1,
    'orderby'=> 'title',
    'order' => 'ASC'
);
if (@$_GET['search-type'] != "") {

    if ($_GET['search-type'] == "name") {
        $q = @$_GET['q'];
        $args['s'] = $q;
        $sp_radio = "";
        $na_radio = "checked";
    }

    if ($_GET['search-type'] == "specialty" && $_GET['sp'] != "") {
        $sp = @$_GET['sp'];
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'member_cat',
                //'field' => 'name',
                'field' => 'id',
                'orderby' => 'title',
                 'order' => 'ASC',
                'terms' => $sp
            )
        );
    }

//$wp_query = new WP_Query($args);
    //$wp_query = new WP_Query($args);
}

//exit;
$posts = get_posts($args);
$terms = get_terms([
    'taxonomy' => "member_cat",
    'hide_empty' => false,
    'orderby' => 'title',
     'order' => 'ASC'
        ]);

?>

<div class="find-a-doctor">
    <style>
        .sb-btn{
            background-color: #ffd878;
            border-color: #ffd878;
            color: #295086;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            padding: 14px 27px;
        }
        .sb-btn:hover{
            background-color: #ffd878;
            border-color: #ffd878;
            color: #295086;
        }
        .search-bar{
            padding: 10px !important;
        }
        .doctor-info-box{
            min-height: 168px;
            background-color: #f0f4fa;
            padding-top: 14px;
            padding-bottom: 14px;
            border: 3px solid #f0f4fa
        }
        .doctor-info-box img{
            border-radius: 50%;
        } 
        .doctor-info-box h4{
            color: #1d4886;
            margin: 0;
        }
        .doc-specialty-name{
            color: #6a6a6a;
            font-size: 15px;
            margin-bottom: 10px;
        }
        .fee{
            color: #6a6a6a
        }
        .timings{
            color: #6a6a6a;
        }
        .find-a-doctor .row{
            margin-bottom: 20px;
        }
        .doctor-info-box:hover {
            border: 3px solid #1d4886;
        }
        .doctor-info-box:hover .book-appointment-btn{
            display: block;
        }
        .book-appointment-btn{
            display: none;
            padding: 13px 5px;
            position: absolute;
            font-size: 15px;
            right: -54px;
            background-color: #1d4886;
            color: #FFF;
            -ms-transform: rotate(27deg);
            -webkit-transform: rotate(270deg);
            transform: rotate(270deg);
            top: 55px;
        }
        .book-appointment-btn:hover{
            color: #FFF;
        }
    </style>
    <script>
        let v = "<select name = 'sp' class = 'col-md-12 search-bar'>";

<?php
if (!empty($terms)):
    $v = "<option value=''>All</option>";
    foreach ($terms as $key => $value) {
        $v .= "<option " . (($_GET['sp'] == $value->term_id) ? 'selected' : '') . " value='$value->term_id'>$value->name</option>";
    }
endif;
?>
        v += "<?= $v ?></select>";
        let q = '<input type="text" required class="col-md-12 form-control search-bar" name="q" />';
        function searchType(tp) {
            if (tp == "name") {
                document.getElementById('search-field').innerHTML = q;
            } else {
                document.getElementById('search-field').innerHTML = v;
            }
        }
    </script>
    <form method="get">
        <div class="row">
            <div class="col-md-4">
            </div>
            <label class="col-md-2"><input onclick="searchType('specialty')" <?= $sp_radio ?> required type="radio" name="search-type" value="specialty" /> Specialty </label>
            <label class="col-md-3"><input onclick="searchType('name')" <?= $na_radio ?> required type="radio" name="search-type" value="name"/> By Name </label>
            <div class="col-md-3">
            </div>
        </div>
        <div class="row">
            <div class="col-md-1">
            </div>
            <div class="col-md-3">
                <h3 style="padding: 10px;">Find a Doctor</h3>
            </div>
            <div class="col-md-5 search-field" id="search-field">
                <?php if ($sp_radio == "checked") { ?>
                    <select name = 'sp' class = 'col-md-12 search-bar'>
                        <?= $v ?>
                    </select>
                <?php } else { ?>
                    <input type="text" required class="col-md-12 form-control search-bar" value="<?= @$_GET['q'] ?>" name="q" />
                <?php } ?>
            </div>
            <div class="col-md-2"><input class="sb-btn" type="submit" value="Search"/> </div>
        </div>
    </form>
    <?php
    if (!empty($posts)):
        $count = 1;
        $days = array("monday" => "Mon", "tuesday" => "Tue", "wednesday" => "Wed", "thursday" => "Thu", "friday" => "Fri", "saturday" => "Sat", "sunday" => "Sun");
        foreach ($posts as $key => $value) {
            $data = get_post_meta($value->ID);
            $terms = get_terms(array('object_ids' => $value->ID, 'orderby' => 'title',  'order' => 'ASC', 'hide_empty' => TRUE, 'taxonomy' => 'member_cat'));
            $_member_timing = json_decode($data['_member_timing'][0]);
            $m_t = [];
            if (is_array($_member_timing) || is_object($_member_timing) && @$_member_timing->day != NULL) {
                if ((is_array(@$_member_timing->day) || is_object($_member_timing))) {
                    foreach (@$_member_timing->day as $mkey => $mvalue) {
                        $m_t[$_member_timing->time->{$mkey}][] = $days[$mkey];
                    }
                }
            }
            if ($count % 2 != 0) {
                echo "<div class='row'>";
            }
            $img = get_the_post_thumbnail_url($value->ID);
            if (trim($img) == "") {
                $theme_root = get_bloginfo('template_url', 'display') . "/images/doctor-2.png";
                $img = $theme_root;
            }
            ?>
            <div class="col-md-5 doctor-info-box">
                <a target="_new" href="<?= home_url('book-appointment?d_id=' . $value->ID) ?>">
                    <div class="col-md-3" style="padding: 0;">
                        <img src="<?= $img ?>" width="100" alt="Doctor Image" />
                    </div>
                    <div class="col-md-9">
                        <h4><?php echo $data['member_firstname'][0] . " " . $data['member_lastname'][0] ?></h4>
                        <div class="doc-specialty-name">
                            <?php
                            foreach ($terms as $tkey => $tvalue) {
                                echo $tvalue->name . ", ";
                            }
                            ?>
                        </div>
                        <div class="fee">
                            <div class="col-md-3" style="padding-left: 0;font-weight: bold">Fees</div>
                            <div class="col-md-9" style="padding: 0">Rs <?= (is_object(@$_member_timing)) ? @$_member_timing->fee : 0 ?></div>
                        </div>
                        <div class="timings">
                            <div class="col-md-3" style="padding-left: 0;font-weight: bold">Timings</div>
                            <div class="col-md-9" style="padding: 0">
                                <?php
                                foreach ($m_t as $mkey => $mvalue) {
                                    echo $mkey . " (" . implode(",", $mvalue) . ")<br/>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 book-appointment-btn">
                        Book Appointment
                    </div>
        <!--                <a target="_new" href="<?= home_url('book-appointment?d_id=' . $value->ID) ?>" class="col-md-4 book-appointment-btn">Book Appointment</a>-->
                </a>
            </div>
            <div class="col-md-1"></div>
            <?php
            if ($count % 2 == 0) {
                echo "</div>";
            }
            $count++;
        }
    endif;
    ?>
</div>