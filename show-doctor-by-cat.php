<?php
$term_name = "";
$term_description = "";
$sp_radio = "checked";
$na_radio = "";
if ($atts['cat_id'] != "") {

    $args = array(
        'post_type' => 'member',
        'tax_query' => array(
            array(
                'taxonomy' => 'member_cat',
                'field' => 'id',
                'terms' => $atts['cat_id']
            )
    ));
    $posts = get_posts($args);
    $term = get_term_by("id", $atts['cat_id'], "member_cat");

    if ($term) {
        $term_name = $term->name;
        $term_description = $term->description;
    }
}
?>

<div class="find-a-doctor">
    <div class="row">
        <div class="col-md-6 doctor-by-cat-left" style=";">
            <h2><?= $term_name ?></h2>
            <?php echo the_post_thumbnail( 'medium_large' ); ?>
            <p><?=$term_description?></p>
        </div>
        <div class="col-md-6 doctor-by-cat-right">
            <style>
                .doctor-by-cat-left{
                    background-color: #1d4886;padding: 34px 24px;
                }
                .doctor-by-cat-left p{padding-top: 25px;}
                .doctor-by-cat-right{
                    padding: 34px 24px;
                }
                .doctor-by-cat-right h2{
                    color: #5c5c5c;
                }
                .doctor-by-cat-left h2,.doctor-by-cat-left p{
                    color: #FFF;
                }
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
                    min-height: 175px;
                    background-color: #f0f4fa;
                    padding-top: 14px;
                    padding-bottom: 14px;
                    margin-bottom: 21px;
                    border: 3px solid #f0f4fa;
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
                    font-size: 16px;
                    text-align: center;
                    right: -61px;
                    background-color: #1d4886;
                    color: #FFF;
                    -ms-transform: rotate(27deg);
                    -webkit-transform: rotate(270deg);
                    transform: rotate(270deg);
                    top: 58px;
                }
                .book-appointment-btn:hover{
                    color: #FFF;
                }
            </style>
            <h2>Weekly Available Specialist</h2>
            <?php
            if (!empty($posts)):
                $count = 1;
                $days = array("monday" => "Mon", "tuesday" => "Tue", "wednesday" => "Wed", "thursday" => "Thu", "friday" => "Fri", "saturday" => "Sat", "sunday" => "Sun");
                foreach ($posts as $key => $value) {
                    $data = get_post_meta($value->ID);
                    $terms = get_terms(array('object_ids' => $value->ID, 'orderby' => 'term_group', 'hide_empty' => TRUE, 'taxonomy' => 'member_cat'));
                    $_member_timing = json_decode($data['_member_timing'][0]);
                    $m_t = [];
                    if (is_array($_member_timing) || is_object($_member_timing) && @$_member_timing->day != NULL) {
                        if ((is_array(@$_member_timing->day) || is_object($_member_timing))) {
                            foreach (@$_member_timing->day as $mkey => $mvalue) {
                                $m_t[$_member_timing->time->{$mkey}][] = $days[$mkey];
                            }
                        }
                    }
                    $img = get_the_post_thumbnail_url($value->ID);
                    ?>

                    <div class="col-md-12 doctor-info-box">
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
                                <div class="col-md-9" style="padding: 0">Rs <?=(is_object(@$_member_timing)) ? @$_member_timing->fee : 0?></div>
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
                        <a target="_new" href="<?= home_url('book-appointment?d_id=' . $value->ID) ?>" class="col-md-4 book-appointment-btn">Book Appointment</a>
                    </div>
                    <?php
                }
            endif;
            ?>
        </div>
    </div>
</div>