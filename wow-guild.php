<?php
/* 
Plugin Name: WoW Guild 
Plugin URI: http://timsworld.nfshost.com/wordpress-plugins/wow-guild-wp-plugin
Description: Shows your Guild Roster  
Version: 1.5
Author: Tim (SeiferTim) Hely
Author URI: http://timsworld.nfshost.com
*/  

class WoWGuild {
    var $plugin_folder = '';

    var $default_options = array(
        'gname' => '',
        'realm' => '',
        'realmtype' => '',
        'showno' => 10,
        'linkback' => false
    );

    

  function wow_guild_head() {
    ?>
    <script type="text/javascript">
    
      
      jQuery(document).ready(function() {
        jQuery('.guild_display').each(function() {
          var t = jQuery(this);
          getGuild(t.children('.guild_gname').val(), t.children('.guild_realm').val(), t.children('.guild_realmtype').val(), t.children('.guild_showno').val(), t.children('.guild_no').val(), '<?php echo get_bloginfo('wpurl'); ?>', 1,t.children('.guild_link').val());
        });
        
      });
   </script>
   <?php  
  }
  
  function WoWGuild() {
        $this->plugin_folder = get_option('home').'/'.PLUGINDIR.'/wow-guild/';
        wp_enqueue_script('jquery');
        wp_enqueue_script('wowhead',"http://www.wowhead.com/widgets/power.js");
        wp_enqueue_script('wow_guild', $this->plugin_folder.'js/guild.js',array('jquery'));    
        wp_enqueue_style('wow_guild_css', $this->plugin_folder.'css/style.css');
        add_action('wp_head', array(&$this, 'wow_guild_head'));
        
    }

    function init() {
        if (!$options = get_option('widget_wow_guild'))
            $options = array();
            
        $widget_ops = array('classname' => 'widget_wow_guild', 'description' => 'WoW Guild');
        $control_ops = array('width' => 250, 'height' => 100, 'id_base' => 'wowguild');
        $name = 'WoW Guild';
        
        $registered = false;
        foreach (array_keys($options) as $o) {
            if (!isset($options[$o]['gname']))
                continue;
                
            $id = "wowguild-$o";
            $registered = true;
            wp_register_sidebar_widget($id, $name, array(&$this, 'widget'), $widget_ops, array( 'number' => $o ) );
            wp_register_widget_control($id, $name, array(&$this, 'control'), $control_ops, array( 'number' => $o ) );
        }
        if (!$registered) {
            wp_register_sidebar_widget('wowguild-1', $name, array(&$this, 'widget'), $widget_ops, array( 'number' => -1 ) );
            wp_register_widget_control('wowguild-1', $name, array(&$this, 'control'), $control_ops, array( 'number' => -1 ) );
        }
    }
    
    

    function widget($args, $widget_args = 1) {
        
        extract($args);
        global $post;

        if (is_numeric($widget_args))
            $widget_args = array('number' => $widget_args);
        $widget_args = wp_parse_args($widget_args, array( 'number' => -1 ));
        extract($widget_args, EXTR_SKIP);
        $options_all = get_option('widget_wow_guild');
        if (!isset($options_all[$number]))
            return;

        $options = $options_all[$number];

        echo $before_widget;
        echo $before_title . ' Guild Roster for ' . $options["gname"] . $after_title;	   
        echo '<div id="guild-'.$number.'" class="guild_display">';
        ?>
        <input type="hidden" class="guild_no" value="<?php echo $number; ?>" />
        <input type="hidden" class="guild_gname" value="<?php echo $options['gname']; ?>" />
        <input type="hidden" class="guild_realm" value="<?php echo $options['realm']; ?>" />
        <input type="hidden" class="guild_realmtype" value="<?php echo $options['realmtype']; ?>" />
        <input type="hidden" class="guild_showno" value="<?php echo $options['showno']; ?>" />
        <input type="hidden" class="guild_link" value="<?php echo $options['linkback']; ?>" />
        <?php
        echo'<img src="'.$this->plugin_folder.'ajax-loader.gif" /></div>';
        echo $after_widget;
    }

    function control($widget_args = 1) {
        global $wp_registered_widgets;
        static $updated = false;
        

        if ( is_numeric($widget_args) )
            $widget_args = array('number' => $widget_args);
        $widget_args = wp_parse_args($widget_args, array('number' => -1));
        extract($widget_args, EXTR_SKIP);
        $options_all = get_option('widget_wow_guild');
        
        if (!is_array($options_all))
            $options_all = array();  
            
        if (!$updated && !empty($_POST['sidebar'])) {
            $sidebar = (string)$_POST['sidebar'];

            $sidebars_widgets = wp_get_sidebars_widgets();
            if (isset($sidebars_widgets[$sidebar]))
                $this_sidebar =& $sidebars_widgets[$sidebar];
            else
                $this_sidebar = array();

            foreach ($this_sidebar as $_widget_id) {
                if ('widget_wow_guild' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number'])) {
                    $widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
                    if (!in_array("wowguild-$widget_number", $_POST['widget-id']))
                        unset($options_all[$widget_number]);
                }
            }
            
            foreach ((array)$_POST['widget_wow_guild'] as $widget_number => $posted) {
                if (!isset($posted['gname']) && isset($options_all[$widget_number]))
                    continue;
                
                $options = array();
                $options['gname'] = stripslashes($posted['gname']);
                $options['realm'] = stripslashes($posted['realm']);
                $options['realmtype'] = $posted['realmtype'];
                $options['showno'] = $posted['showno'];
                $options['linkback'] = $posted['linkback'];
                
                $options_all[$widget_number] = $options;
            }
            update_option('widget_wow_guild', $options_all);
            $updated = true;
        }

        if (-1 == $number) {
            $number = '%i%';
            $values = $this->default_options;
        }
        else {
            $values = $options_all[$number];
        }        
        
        ?>
        <label for="widget_wow_guild[<?php echo $number; ?>][gname]">Guild Name:</label>
        <input class="widefat" id="widget_wow_guild-<?php echo $number; ?>-gname" name="widget_wow_guild[<?php echo $number; ?>][gname]" type="text" value="<?php echo stripslashes($values['gname']); ?>" />
        <label for="widget_wow_guild[<?php echo $number; ?>][realm]">Realm:</label>
        <input class="widefat" id="widget_wow_guild-<?php echo $number; ?>-realm" name="widget_wow_guild[<?php echo $number; ?>][realm]" type="text" value="<?php echo stripslashes($values['realm']); ?>" />
        <label for="widget_wow_guild[<?php echo $number; ?>][realmtype]">Realm Type:</label>
        <?php echo $values['realmtype']; ?>
        <select class="widefat" style="width: 100;" id="widget_wow_guild-<?php echo $number; ?>-realmtype" name="widget_wow_guild[<?php echo $number; ?>][realmtype]" />
          <option value="US"<?php echo $values['realmtype'] == 'US' ? ' selected="selected"' : ''; ?>>US</option>
          <option value="EU"<?php echo $values['realmtype'] == 'EU' ? ' selected="selected"' : ''; ?>>EU</option>
        </select>
        <label for="widget_wow_guild[<?php echo $number; ?>][showno]">Show:</label>
        <input class="widefat" id="widget_wow_guild-<?php echo $number; ?>-showno" name="widget_wow_guild[<?php echo $number; ?>][showno]" type="text" value="<?php echo stripslashes($values['showno']); ?>" />
        <br/>
        <label for="widget_wow_guild[<?php echo $number; ?>][linkback]">Show an Optional link back to Tim's World?</label>
        <input id="widget_wow_guild-<?php echo $number; ?>-linkback" name="widget_wow_guild[<?php echo $number; ?>][linkback]" type="checkbox" value="1"<?php echo $values['linkback'] ? ' checked' : ''; ?> />
        <br />
        <?php
        
    }

}

function wowguild_addFromShortcode($atts, $content = null) {
  //echo $before_widget;
  extract(shortcode_atts(array(
      "name" => '',
      "realm" => '',
      "realmtype" => 'US',
      "showno" => '10',
      "link" => 0
    ), $atts));
  $randNo = rand(0, pow(10, 5));
  $results = '<div id="guild-'.$randNo.'" class="guild_display">';
  $results .= '<input type="hidden" class="guild_no" value="'.$randNo.'" />';
  $results .= '<input type="hidden" class="guild_gname" value="'.$name.'" />';
  $results .= '<input type="hidden" class="guild_realm" value="'.stripslashes($values['realm']).'" />';
  $results .= '<input type="hidden" class="guild_realmtype" value="'.$realmtype.'" />';
  $results .= '<input type="hidden" class="guild_showno" value="'.$showno.'" />';
  $results .= '<input type="hidden" class="guild_link" value="'.$link.'" />';
  $results .= '<img src="'.get_option('home').'/'.PLUGINDIR.'/wow-guild/'.'ajax-loader.gif" /></div>';
  //echo $after_widget;
  return $results;
}

$wowguild = new WoWGuild();
add_action('widgets_init', array($wowguild, 'init'));
add_shortcode("wowguild", "wowguild_addFromShortcode");
?>