<?php
$_plugin_list .= '	
<!-- jvectormap -->
<link rel="stylesheet" href="template/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
<!-- Daterange picker -->
<link rel="stylesheet" href="template/plugins/daterangepicker/daterangepicker.css">

<link rel="stylesheet" href="mod_statistic/style.css">

<!-- jvectormap -->
<script src="template/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="template/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- Sparkline -->
<script src="template/plugins/sparkline/jquery.sparkline.min.js"></script>
<!-- daterangepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
<script src="template/plugins/daterangepicker/daterangepicker.js"></script>

<script src="mod_statistic/script.js"></script>';

?>
          <?php /* Map box */ ?>
          <div class="box box-solid bg-light-blue-gradient">
            <div class="box-header">
              <?php /* tools box */ ?>
              <div class="pull-right box-tools">
                <button type="button" class="btn btn-primary btn-sm daterange pull-right" data-toggle="tooltip" title="Date range">
                  <i class="fa fa-calendar"></i></button>
                <button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="Collapse" style="margin-right: 5px;">
                  <i class="fa fa-minus"></i></button>
              </div>
              <?php /* /. tools */ ?>

              <i class="fa fa-map-marker"></i>

              <h3 class="box-title">
                <?php echo $language['visitors']?>
              </h3>
            </div>
            <div class="box-body">
              <div id="world-map" style="height: 250px; width: 100%;"></div>
            </div>
            <?php /* /.box-body*/ ?>
            <div class="box-footer no-border">
              <div class="row">
                <div class="col-xs-6 text-center" style="border-right: 1px solid #f4f4f4">
                  <div class="sparkline" id="sparkline-1"></div>
                  <div class="knob-label"><?php echo $language['visitors']?></div>
                </div>
                <?php /* ./col */ ?>
                <div class="col-xs-6 text-center">
                  <div class="sparkline" id="sparkline-2"></div>
                  <div class="knob-label"><?php echo $language['new_visitors']?></div>
                </div>
                <?php /* ./col */ ?>
              </div>
              <?php /* /.row */ ?>
            </div>
          </div>
          <?php /* /.box */ ?>
