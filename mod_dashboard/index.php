    <?php /* Content Header (Page header) */?>
    <section class="content-header">
      <h1>
        <?php echo $language['dashboard']?>
        <small><?php /*Optional description*/?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> <?php echo $language['home']?></a></li>
        <li class="active"><?php echo $language['dashboard']?></li>
      </ol>
    </section>

    <?php /* Main content */?>
    <section class="content">

      <?php /* Your Page Content Here */?>
        <?php /* right col (We are only adding the ID to make the widgets sortable)*/ ?>
        <section class="col-lg-5 connectedSortable">
			<?php require ("mod_statistic/index.php"); ?>  
        </section>  
    </section>
    <?php /* /.content */?>
     