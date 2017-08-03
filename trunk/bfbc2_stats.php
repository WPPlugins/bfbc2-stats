<?php
/*
Plugin Name: BFBC2 Stats
Plugin URI: http://www.fps-gamer/bfbc2-stats/
Description: A sidebar widget to display your Battlefield Bad Company 2 stats powered by <a href="http://bfbcs.com">bfbcs.com</a>.
Version: 1.2.1
Author: Ozgur Uysal
Author URI: http://www.fps-gamer.net

Copyright(c)2010  Ozgur Uysal  (email : admin@fps-gamer.net)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function bfbc2_stats_get_dir($type) 
{
  if(!defined('WP_CONTENT_URL'))
    define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
  if (!defined('WP_CONTENT_DIR'))
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
  if ($type=='path') 
  { 
    return WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__)); 
  }
  else 
  { 
    return WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)); 
  }
}

function bfbc2_stats()
{
  $cache_dir = './wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/cache/';

  if(is_writable($cache_dir) == 0)
  {
    echo '<span class="servererrmsg">Site Admin: Please make sure "' . $cache_dir . '" folder is writable by webserver</span><br />';
    return;
  }

  widget_bfbc2_checkplayername();
  $cache = new cache();

  if ($cache->cval == 0)
  {
    $data = get_option("widget_bfbc2_stats");

    $url = 'http://api.bfbcs.com/api/'.$data['bfbc2_platform'].'';
    $postdata = 'players='.$data['bfbc2_playername'].'&fields=all';

    if(!function_exists(curl_init))
    {
      echo '<span class="servererrmsg">Site Admin: cURL support is disabled in your php.ini! Please enable it or contact your web service provider.</span>';
      return;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    $playerstats = curl_exec($ch);
    curl_close($ch);

    $playerstats = json_decode($playerstats,true);

    $player_name = $playerstats['players'][0]['name'];
    $rankname = explode(" ", $playerstats['players'][0]['rank_name']);

    foreach($rankname as $key => $value)
    {
       if($value != "I" && $value != "II" && $value != "III")
         $value = strtolower($value);
       $value = ucwords($value);

      $ranks[] = $value;
    }

    $rank_name = implode(" ", $ranks);

    if($data['bfbc2_platform'] == 'pc')
      $platform = 'PC';
    elseif($data['bfbc2_platform'] == 'ps3')
      $platform = 'Play Station 3';
    elseif($data['bfbc2_platform'] == '360')
      $platform = 'XBOX 360';

    $platform_img = '<img src ="'.get_bloginfo('wpurl').'/wp-content/plugins/' . plugin_basename(dirname(__FILE__)) . '/img/platforms/' . $data['bfbc2_platform'] . '.png" title="Platform : '.$platform.'"/>';
    
    if($playerstats['players'][0]['veteran'] < 10)
      $veteran_status_img = '<img src ="'.get_bloginfo('wpurl').'/wp-content/plugins/' . plugin_basename(dirname(__FILE__)) . '/img/veteran/' . $playerstats['players'][0]['veteran'] . '.png" title="Veteran Status"/>';
    else
    {
      $temp_veteran = str_split($playerstats['players'][0]['veteran']);
      $veteran_status_img = '<img src ="'.get_bloginfo('wpurl').'/wp-content/plugins/' . plugin_basename(dirname(__FILE__)) . '/img/veteran/' . $temp_veteran[0] . '.png" title="Veteran Status"/><img src ="'.get_bloginfo('wpurl').'/wp-content/plugins/' . plugin_basename(dirname(__FILE__)) . '/img/veteran/' . $temp_veteran[1] . '.png" title="Veteran Status"/>';
    }

    $lastupdate = date('d M Y, H:i, e', strtotime($playerstats['players'][0]['date_lastupdate']));
    $rank = $playerstats['players'][0]['rank'];
    $score = $playerstats['players'][0]['score'];
    $kills = $playerstats['players'][0]['kills'];
    $deaths = $playerstats['players'][0]['deaths'];
    $ratio = @($kills/$deaths);
    $time_seconds = round($playerstats['players'][0]['time']);

    //Change seconds to hh:mm:ss
    $hh = intval($time_seconds / 3600);
    $ss_remaining = ($time_seconds - ($hh * 3600));
    $mm = intval($ss_remaining / 60);
    $ss = ($ss_remaining - ($mm * 60));
    $time = $hh . "h " . $mm ."m " . $ss ."s";

    $rank_image = "";

    if(isset($rank))
    {
      if((strlen($rank)) < 2)
        $rank_image = '<img style="border:none; padding:none; background:none;" src="http://files.bfbcs.com/img/bfbcs/ranks_big/r00'.$playerstats['players'][0]['rank'].'.png" />';
      else
        $rank_image = '<img style="border:none; padding:none; background:none;" src="http://files.bfbcs.com/img/bfbcs/ranks_big/r0'.$playerstats['players'][0]['rank'].'.png" />';
    }

    ?>
    <link rel="stylesheet" href="<?php echo bfbc2_stats_get_dir('url') ?>/style.css" type="text/css" media="screen" />

    <table class="bfbc2">
    <?php 
    if(isset($playerstats)) 
    {
      if($player_name == null)
      {
        echo '<span class="servererrmsg">The player <strong>"'.$data['bfbc2_playername'].'"</strong> is currently not in <a href="http://bfbcs.com" target="_blank">bfbcs.com</a> database!</span></table>';
        return;
      }
    ?>
        <tr class="bfbc2">
            <td class="bfbc2"><span style="font-size:medium; font-weight:bold;"><?php echo esc_attr_e($player_name); ?></span></td>
        </tr>
        <tr class="bfbc2">
            <td class="bfbc2" style="padding-bottom:5px;"><?php echo $rank_image; ?></td>
        </tr>
        <tr class="bfbc2">
            <td class="bfbc2"><span style="font-weight:bold"><?php echo $rank_name; ?></span></td>
        </tr>
        <tr class="bfbc2">
            <td class="bfbc2"><?php widget_bfbc2_rank_progress($rank, $score) ?></td>
        </tr>
        <tr class="bfbc2">
            <td class="bfbc2">
                <?php 
                echo $platform_img . "&nbsp;";
                echo widget_bfbc2_bestkit($playerstats) . "&nbsp;";
                echo $veteran_status_img;
                ?>
            </td>
        </tr>
        <tr class="bfbc2">
            <td class="bfbc2"><?php echo widget_bfbc2_bestweapon($playerstats); ?></td>
        </tr>
        <tr class="bfbc2">
            <td class="bfbc2">
                <table width="100%">
                    <tr class="bfbc2">
                        <td class="bfbc2r">Score</td><td width="5%" class="bfbc2c"> : </td><td class="bfbc2l"><?php echo number_format($score, 0, " ", " "); ?></td>
                    </tr>
                    <tr class="bfbc2">
                        <td class="bfbc2r">Kills</td><td class="bfbc2c"> : </td><td class="bfbc2l"><?php echo number_format($kills, 0, " ", " "); ?></td>
                    </tr>
                    <tr class="bfbc2">
                        <td class="bfbc2r">Deaths</td><td class="bfbc2c"> : </td><td class="bfbc2l"><?php echo number_format($deaths, 0, " ", " "); ?></td>
                    </tr>
                    <tr class="bfbc2">
                        <td class="bfbc2r">Ratio</td><td class="bfbc2c"> : </td><td class="bfbc2l"><?php echo number_format($ratio, 2, ".", " "); ?></td>
                    </tr>
                    <tr class="bfbc2">
                        <td class="bfbc2r">Time</td><td class="bfbc2c"> : </td><td class="bfbc2l"><?php echo $time; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="bfbc2">
                <div class="divider"></div>
                <div class="ftr">Update: <?php echo $lastupdate; ?></div>
            </td>
        </tr>
        <?php 
      $cache->close();
    }
    else 
    {
      ?>
        <div class="servererrmsg">
          <span><a href="http://bfbcs.com" target="_blank">bfbcs.com server is probably overloaded. Try later again!</a></span></table>
        </div>
      <?php
    }
  }
  echo '</table><br />';
}

function widget_bfbc2_stats($args) 
{
  $data = get_option("widget_bfbc2_stats");
  
  if(!isset($data['bfbc2_title']) || $data['bfbc2_title'] == false)
    $data['bfbc2_title'] = 'My BFBC2 Stats';
  
  extract($args);
  echo $before_widget;
  echo $before_title;
  echo $data['bfbc2_title'];
  echo $after_title;
  bfbc2_stats();
  echo $after_widget;
}

function bfbc2_stats_init()
{
  register_sidebar_widget('BFBC2 Stats', 'widget_bfbc2_stats');
  register_widget_control('BFBC2 Stats', 'bfbc2_stats_control');
}

add_action("plugins_loaded", "bfbc2_stats_init");

function bfbc2_stats_control()
{
  $data = get_option("widget_bfbc2_stats");

  if (!is_array( $data ))
  {
    $data = array(
        'bfbc2_title' => 'My BFBC2 Stats',
        'bfbc2_playername' => 'Your Player Name',
        'bfbc2_platform' => 'pc',
        'bfbc2_cachetime' => '3600'
      );
  }

  $platforms = array(
        'pc' => 'PC',
        'ps3' => 'PS3', 
        '360' => 'XBOX360');

  if(isset($_POST['bfbc2stats-Submit']))
  {
    $data['bfbc2_title'] = esc_attr($_POST['widget_bfbc2_title']);
    $data['bfbc2_playername'] = $_POST['widget_bfbc2_playername'];
    $data['bfbc2_platform'] = esc_attr($_POST['widget_bfbc2_platform']);
    $data['bfbc2_cachetime'] = esc_attr($_POST['widget_bfbc2_cachetime']);
    update_option('widget_bfbc2_stats', $data);
  }
  ?>
  <p>
    <label for="widget_bfbc2_title">Title:<br />
    <input type="text" size="34" id="widget_bfbc2_title" name="widget_bfbc2_title" value="<?php echo $data['bfbc2_title']; ?>" />
    </label>
  </p>
  <p>
    <label for="widget_bfbc2_playername">Player Name:<br />
    <input type="text" size="34" id="widget_bfbc2_playername" name="widget_bfbc2_playername" value="<?php echo $data['bfbc2_playername']; ?>" />
    </label>
  </p>
  <p>
    <label for="widget_bfbc2_platform">Platform:<br />
    <select id="widget_bfbc2_platform" name="widget_bfbc2_platform">

    <?php
    foreach($platforms as $key => $value)
    {
      if($data['bfbc2_platform'] == $key)
        $selected = " selected=\"selected\"";
      else
        $selected = false;
      
      echo "<option" . $selected . " value=\"".$key."\">".$value."</option>";
    }
    ?>
    </select>
    </label>
  </p>
  <p>
    <label for="widget_bfbc2_playername">Cache Time (in seconds):<br />
    <input type="text" size="34" id="widget_bfbc2_cachetime" name="widget_bfbc2_cachetime" value="<?php if($data['bfbc2_cachetime'] != null) echo $data['bfbc2_cachetime']; else echo '3600'; ?>" />
    </label>
    <input type="hidden" id="bfbc2stats-Submit" name="bfbc2stats-Submit" value="1" />
  </p>
  <?php
}

//If playername was changed, empty cache to changes to take effect immediately!
function widget_bfbc2_checkplayername()
{
  $data = get_option("widget_bfbc2_stats");
  $cache_file = './wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/cache/bfbc2_cache.txt';

  if(file_exists($cache_file))
  {
    //Get Player Name from cache
    $html = file_get_contents($cache_file);

    $dom = new domDocument;
    $dom->loadHTML($html);
    $dom->preserveWhiteSpace = false;
    $tables = $dom->getElementsByTagName('table');

    foreach($tables as $table)
    {
      $tablerows = $table->getElementsByTagName('tr');
      $playername[] = $tablerows->item(0)->nodeValue;
    }

    $playername = $playername[0];

    if(strcasecmp(trim($data['bfbc2_playername']), trim($playername)) != 0)
    {
      unlink($cache_file);
    }
  }
}

function widget_bfbc2_rank_progress($rank, $score)
{
  $rank_list = array( '1' => 6500,
                      '2' => 11000,
                      '3' => 18500,
                      '4' => 28000,
                      '5' => 40000,
                      '6' => 53000,
                      '7' => 68000,
                      '8' => 84000,
                      '9' => 100000,
                      '10' => 120000,
                      '11' => 138000,
                      '12' => 158000,
                      '13' => 179000,
                      '14' => 200000,
                      '15' => 224000,
                      '16' => 247000,
                      '17' => 272000,
                      '18' => 297000,
                      '19' => 323000,
                      '20' => 350000,
                      '21' => 377000,
                      '22' => 405000,
                      '23' => 437000,
                      '24' => 472000,
                      '25' => 537000,
                      '26' => 620000,
                      '27' => 720000,
                      '28' => 832000,
                      '29' => 956000,
                      '30' => 1090000,
                      '31' => 1240000,
                      '32' => 1400000,
                      '33' => 1550000,
                      '34' => 1730000,
                      '35' => 1900000,
                      '36' => 2100000,
                      '37' => 2300000,
                      '38' => 2530000,
                      '39' => 2700000,
                      '40' => 2928000,
                      '41' => 3142000,
                      '42' => 3378000,
                      '43' => 3604000,
                      '44' => 3852000,
                      '45' => 4090000,
                      '46' => 4350000,
                      '47' => 4600000,
                      '48' => 4872000,
                      '49' => 5134000,
                      '50' => 5400000);

  $score_between_levels = $rank_list[$rank+1] - $rank_list[$rank];
  $score_achievement = $score - $rank_list[$rank];
  $score_percentage = @($score_achievement / $score_between_levels);
  $bar_width = round($score_percentage * 100);
  $image_path = get_bloginfo('wpurl').'/wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/img/';

  if($score >= 5400000)
    echo '<div class="general_stars_box"><div><img src="'.$image_path.'general.png" class="general_stars" /></div></div>';
  else
    echo '<div class="rankbarbg"><div><img src="'.$image_path.'bar.png" width="'.$bar_width.'px" height="5px" class="rankbar" /></div></div>';
}

function widget_bfbc2_bestkit($playerstats)
{
  $kits = $playerstats['players'][0]['kits'];
  foreach ( $kits as $key => $value )
  {
    $kit_easy_name[] = $value['name'] ;
    $kit_score[] = $value['score'];
    $kit_short_name[] = $key;
  }

  $temp_kit_names = array_combine($kit_short_name, $kit_easy_name);
  $temp_kits_scores = array_combine($kit_easy_name, $kit_score);
  $bestkitscore = max($temp_kits_scores);

  foreach ($temp_kits_scores as $key => $value)
  { 
    if($value == $bestkitscore)
      $bestkit = $key;

    foreach ($temp_kit_names as $key => $value)
    {
      if($value == $bestkit)
        $bestkit_img = $key.".png";
    }
  }

  $bestkit_img = '<img src ="'.get_bloginfo('wpurl').'/wp-content/plugins/' . plugin_basename(dirname(__FILE__)) . '/img/kits/' . $bestkit_img .'" title="Favorite Kit : ' . $bestkit . '"/>';

  return $bestkit_img;
}

function widget_bfbc2_bestweapon($playerstats)
{
  $weapons = $playerstats['players'][0]['weapons'];
  foreach ( $weapons as $key => $value )
  {
    $weapon_easy_name[] = $value['name'] ;
    $weapon_kills[] = $value['kills'];
    $weapon_short_name[] = $key;
  }

  $temp_weapon_names = array_combine($weapon_short_name, $weapon_easy_name);
  $temp_weapons_scores = array_combine($weapon_easy_name, $weapon_kills);
  $bestweaponkills = max($temp_weapons_scores);

  foreach ($temp_weapons_scores as $key => $value)
  { 
    if($value == $bestweaponkills)
      $bestweapon = $key;

    foreach ($temp_weapon_names as $key => $value)
    {
      if($value == $bestweapon)
        $bestweapon_img = $key.".png";
    }
  }

  $bestweapon_img = '<img src ="'.get_bloginfo('wpurl').'/wp-content/plugins/' . plugin_basename(dirname(__FILE__)) . '/img/weapons/' . $bestweapon_img .'" title="Favorite Weapon : ' . $bestweapon . '"/>';

  return $bestweapon_img;
}

// *********************** Cache Class ***************************
class cache
{
  var $cache_time = 3600;//How much time will keep the cache files in seconds.

  var $caching = false;
  var $file = '';
  var $cval = 0;

  function cache()
  {
    $data = get_option("widget_bfbc2_stats");

    $this->cache_dir = './wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/cache/';
    if (isset($data['bfbc2_cachetime'])) $this->cache_time = $data['bfbc2_cachetime'];
    //Constructor of the class
    $this->file = $this->cache_dir . 'bfbc2_cache.txt';
    if ( file_exists ( $this->file ) && ( filemtime ( $this->file ) + $this->cache_time ) > time() )
    {
      //Grab the cache:
      $handle = fopen( $this->file , "r");
      do 
      {
        $data = fread($handle, 8192);
        if (strlen($data) == 0) 
        {
          break;
        }
        $this->cval = 1;
        echo $data;
      } 
      while (true);
      fclose($handle);
    }
    else
    {
      //create cache :
      $this->caching = true;
      ob_start();
    }
  }

  function close()
  {
    //You should have this at the end of each page
    if ( $this->caching )
    {
      //You were caching the contents so display them, and write the cache file
      $data = ob_get_clean();
      echo $data;
      $fp = fopen( $this->file , 'w' );
      fwrite ( $fp , $data );
      fclose ( $fp );
    }
  }
}
?>