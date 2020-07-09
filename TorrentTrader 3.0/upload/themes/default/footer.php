<?php function_exists('T_') or die;

			if ($site_config["MIDDLENAV"]){
				middleblocks();
			} //MIDDLENAV ON/OFF END
			?>
          </td>
          <!-- END MAIN COLUM -->
          <?php if ($site_config["RIGHTNAV"]){ ?>
          <!-- START RIGHT COLUMN -->
          <td valign="top" width="170">
		  <?php rightblocks(); ?>
          </td>
          <!-- END RIGHT COLUMN -->
          <?php } ?>
        </tr>
    </table>
  </div>
<!-- End Content -->
      <!-- START FOOTER CODE -->
      <div class='credits'>
        <?php
        //
        // *************************************************************************************************************************************
        //			PLEASE DO NOT REMOVE THE POWERED BY LINE, SHOW SOME SUPPORT! WE WILL NOT SUPPORT ANYONE WHO HAS THIS LINE EDITED OR REMOVED!
        // *************************************************************************************************************************************
        printf (T_("POWERED_BY_TT")." -|- ", $site_config["ttversion"]);
        $totaltime = array_sum(explode(" ", microtime())) - $GLOBALS['tstart'];
        printf(T_("PAGE_GENERATED_IN"), $totaltime);
        print ("<br /><a href=\"https://www.torrenttrader.org\" target=\"_blank\">www.torrenttrader.org</a> -|- <a href='rss.php'><img src='".$site_config["SITEURL"]."/images/icon_rss.gif' border='0' width='13' height='13' alt='' /></a> -|- <a href='rss.php'>".T_("RSS_FEED")."</a> -|- <a href='rss.php?custom=1'>".T_("FEED_INFO")."</a><br />Theme By: <a href='http://nikkbu.info' target='_blank'>Nikkbu</a>");
        //
        // *************************************************************************************************************************************
        //			PLEASE DO NOT REMOVE THE POWERED BY LINE, SHOW SOME SUPPORT! WE WILL NOT SUPPORT ANYONE WHO HAS THIS LINE EDITED OR REMOVED!
        // *************************************************************************************************************************************
        
        ?>
      </div>
      <!-- END FOOTER CODE -->
</div>
</body>
</html>
<?php ob_end_flush(); ?>
