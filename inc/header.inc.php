
    <?php /* Logo */?>
    <a href="home.php" class="logo">
      <?php /* mini logo for sidebar mini 50x50 pixels */?>
      <span class="logo-mini"><img src="images/sidelogo.png" alt=""></span>
      <?php /* logo for regular state and mobile devices */?>
      <span class="logo-lg"><b>FireFly</b></span>
    </a>

    <?php /* Header Navbar */?>
    <nav class="navbar navbar-static-top" role="navigation">
      <?php /* Sidebar toggle button*/?>
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <?php /* Navbar Right Menu */?>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <?php /* Messages: style can be found in dropdown.less*/?>
          <li class="dropdown messages-menu">
            <?php /* Menu toggle button */?>
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-envelope-o"></i>
              <span class="label label-success">4</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 4 messages</li>
              <li>
                <?php /* inner menu: contains the messages */?>
                <ul class="menu">
                  <li><?php /* start message */?>
                    <a href="#">
                      <div class="pull-left">
                        <?php /* User Image */?>
                        <img src="template/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                      </div>
                      <?php /* Message title and timestamp */?>
                      <h4>
                        Support Team
                        <small><i class="fa fa-clock-o"></i> 5 mins</small>
                      </h4>
                      <?php /* The message */?>
                      <p>Why not buy a new awesome theme?</p>
                    </a>
                  </li>
                  <?php /* end message */?>
                </ul>
                <?php /* /.menu */?>
              </li>
              <li class="footer"><a href="#">See All Messages</a></li>
            </ul>
          </li>
          <?php /* /.messages-menu */?>

          <?php /* Notifications Menu */?>
          <li class="dropdown notifications-menu">
            <?php /* Menu toggle button */?>
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning">10</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 10 notifications</li>
              <li>
                <?php /* Inner Menu: contains the notifications */?>
                <ul class="menu">
                  <li><?php /* start notification */?>
                    <a href="#">
                      <i class="fa fa-users text-aqua"></i> 5 new members joined today
                    </a>
                  </li>
                  <?php /* end notification */?>
                </ul>
              </li>
              <li class="footer"><a href="#">View all</a></li>
            </ul>
          </li>
          <?php /* Tasks Menu */?>
          <li class="dropdown tasks-menu">
            <?php /* Menu Toggle Button */?>
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-flag-o"></i>
              <span class="label label-danger">9</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 9 tasks</li>
              <li>
                <?php /* Inner menu: contains the tasks */?>
                <ul class="menu">
                  <li><?php /* Task item */?>
                    <a href="#">
                      <?php /* Task title and progress text */?>
                      <h3>
                        Design some buttons
                        <small class="pull-right">20%</small>
                      </h3>
                      <?php /* The progress bar */?>
                      <div class="progress xs">
                        <?php /* Change the css width attribute to simulate progress */?>
                        <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                          <span class="sr-only">20% Complete</span>
                        </div>
                      </div>
                    </a>
                  </li>
                  <?php /* end task item */?>
                </ul>
              </li>
              <li class="footer">
                <a href="#">View all tasks</a>
              </li>
            </ul>
          </li>
          <?php /* User Account Menu */?>
          <li class="dropdown user user-menu">
            <?php /* Menu Toggle Button */?>
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <?php /* The user image in the navbar*/?>
              <img src="<?php echo $_SESSION['MEMBER_AVATAR']?>" class="user-image" alt="<?php echo $_SESSION['MEMBER_NAME']?>">
              <?php /* hidden-xs hides the username on small devices so only the image appears. */?>
              <span class="hidden-xs"><?php echo $_SESSION['MEMBER_NAME']?></span>
            </a>
            <ul class="dropdown-menu">
              <?php /* The user image in the menu */?>
              <li class="user-header">
                <img src="<?php echo $_SESSION['MEMBER_AVATAR']?>" class="img-circle" alt="User Image">

                <p>
                  <?php echo $_SESSION['MEMBER_NAME']?> - <?php echo $_SESSION['MEMBER_POSITION']?>
                  <small><?php echo $language['member_since'].' '.dateShow($_SESSION['language'],'','M','Y','','',$_SESSION['MEMBER_CREATE']);?></small>
                </p>
              </li>

              <?php /* Menu Footer*/?>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="?profile" class="btn btn-default btn-flat"><?php echo $language['profile']?></a>
                </div>
                <div class="pull-right">
                  <a href="signout.php" onclick="signout_confirm('<?php echo $language['comfirm_logout']?>'); return false;" class="btn btn-default btn-flat"><?php echo $language['sign_out']?></a>
                </div>
              </li>
            </ul>
          </li>
          <?php /* Control Sidebar Toggle Button */?>
          <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
        </ul>
      </div>
    </nav>