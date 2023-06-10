<?php // (C) Copyright Bobbing Wide 2012-2017
if ( !defined( "OIK_ACTIVATION_INCLUDED" ) ) {
define( "OIK_ACTIVATION_INCLUDED", "3.2.0" );


if ( function_exists( "oik_plugin_lazy_activation" ) ) {
 // It's already defined so we don't need this lot
} else { 

/** 
 * Produce an install plugin link
 *
 * @param string $plugin plugin slug
 * @return string link for the plugin install
 */
function oik_plugin_install_plugin( $plugin ) {
	$path = "update.php?action=install-plugin&plugin=$plugin";
	$url = admin_url( $path );
	$url = wp_nonce_url( $url, "install-plugin_$plugin" ); 
	$link = '<a href="';
	$link .= $url;
	$link .= '">';
	$link .= sprintf( __( 'Install %1$s', null ), $plugin ) ;
	$link .= "</a>";
	return $link ;
}

/**
 * Produces an "activate" plugin link
 *
 * We probably don't need plugin_status, s(earch) or paged parameters
 *
 * `
   http://example.com/wp-admin/plugins.php?
     action=activate
     &plugin=oik%2Foik.php
     &plugin_status=all
     &paged=1
		 &s
     &_wpnonce=a53a158be5
 * `
 *
 * @param string $plugin - relative plugin file e.g. oik/oik-header.php
 * We may not be activating the main plugin, so we need the relative path filename of the plugin to activate
 * @param string $plugin_name - e.g. oik-header
 * @return string link to enable activation - which user must choose
*/                              
function oik_plugin_activate_plugin( $plugin, $plugin_name) {
	$path = "plugins.php?action=activate&plugin_status=all&paged=1&s&plugin=$plugin";
	$url = admin_url( $path );
	$url = wp_nonce_url( $url, "activate-plugin_$plugin" ); 
	$link = '<a href="';
	$link .= $url;
	$link .= '">';
	$link .= sprintf( __( 'Activate %1$s', null ), $plugin_name );
	$link .= "</a>";
	return $link;
} 
 
/**
 * Create an Update plugin link
 *
 * @param string $plugin the plugin slug
 * @return string the update link
 */
function oik_plugin_update_plugin( $plugin ) {
	$path = "update.php?action=upgrade-plugin&plugin=$plugin";
	$url = admin_url( $path );
	$url = wp_nonce_url( $url, "upgrade-plugin_$plugin" ); 
	$link = '<a href="';
	$link .= $url;
	$link .= '">'; 
	$link .= sprintf( __( 'Upgrade %1$s', null ), $plugin );
	$link .= "</a>";
	return $link;
}

/** 
 * Find out if we think the plugin is installed but not activated or not even installed
 * 
 * @param string $plugin - the plugin file name ( without plugin path info? )
 * @return string - null if it's not installed or plugin to be activated
 C:\apache\htdocs\wordpress\wp-content\plugins\oik\shortcodes\oik-bob-bing-wide.php(289:0) 2012-05-23T07:52:15+00:00 696 cf=the_content bw_get_plugins(4)  Array
(

    [oik/oik.php] => Array
        (
            [Name] => oik base plugin
            [PluginURI] => http://www.oik-plugins.com/oik
            [Version] => 1.13
            [Description] => Lazy smart shortcodes for displaying often included key-information and other WordPress content
            [Author] => bobbingwide
            [AuthorURI] => http://www.bobbingwide.com
            [TextDomain] => 
            [DomainPath] => 
            [Network] => 
            [Title] => oik base plugin
            [AuthorName] => bobbingwide
        )

)

 */
function oik_plugin_check_installed_plugin( $plugin ) { 
  $plugin_to_activate = null;
  $needle = "/$plugin.php"; 
  
  $plugins = get_plugins();
  //bw_trace2( $plugins ); 
  if ( count( $plugins )) {
    foreach ( $plugins as $plugin_name => $plugin_details ) {
      if ( strpos( $plugin_name, $needle ) ) {
        //bw_trace2( $plugin_name, $needle );
        $plugin_to_activate['Name'] = $plugin_details['Name'];
        $plugin_to_activate['file'] = $plugin_name;
        break;
      }  
    }
  }
  return( $plugin_to_activate );
}


/**
 * Test if the plugin is activated
 *
 * This won't work for Multisite since it doesn't find the network activated plugins
 * Even if it did, the admin may not be able to do anything.
*/
function oik_plugin_is_plugin_activated( $plugin ) {
  $active_plugins = get_option('active_plugins');
  $activated = false;
  foreach ( $active_plugins as $key => $active_plugin ) {
    $bn = basename( $active_plugin, '.php' );
    if ( $plugin == $bn ) {
      $activated = true;
    }  
  }  
  return( $activated );
}

/** 
 * oik_plugin_oik_install_link
  
    http://example.com/wp-admin/update.php?action=install-plugin&plugin=oik&_wpnonce=eb1c632af5
    http://example.com/wp-admin/plugin-install.php?tab=search&s=oik&plugin-search-input=Search+Plugins
    
 * For versions of oik before 1.14 the $problem is not passed so we have to find out ourselves
 * if oik happens to be installed but is the wrong version. Tricky isn't it? 
 */
function oik_plugin_oik_install_link( $plugin, $problem="missing" ) { 
  if ( $problem == "missing"  ) {
    /* Is it missing or just inactive ? */
    $plugin_to_activate = oik_plugin_check_installed_plugin( $plugin );
    if ( $plugin_to_activate ) {
      $link = oik_plugin_activate_plugin( $plugin_to_activate['file'], $plugin_to_activate['Name'] );
    } else {
      $link = oik_plugin_install_plugin( $plugin );
    }
  } elseif ( $problem == null ) {
    $plugin_to_activate = oik_plugin_check_installed_plugin( $plugin );
    if ( $plugin_to_activate ) {
      $activated =  oik_plugin_is_plugin_activated( $plugin );  
      if ( $activated ) {
        $link = oik_plugin_update_plugin( $plugin );
      } else {
        $link = oik_plugin_activate_plugin( $plugin_to_activate['file'], $plugin_to_activate['Name'] );
      }  
    } else {
      $link = oik_plugin_install_plugin( $plugin );
    }  
  } else {
    $link = oik_plugin_update_plugin( $plugin );
  }  
  return( $link );
}
 
/**
 *

 * Display a message when setup is not fully functional due to the dependencies not being activated or installed
 * Note: We can't use oik APIs here as we don't know if it's activated.
 * If the message is issued due to a version mismatch then there is a chance that one plugin attempts to use
 * functions that are not available in the dependent plugin. How do we manage this?
*/
if ( !function_exists( "oik_plugin_plugin_inactive" ) ) {
function oik_plugin_plugin_inactive( $plugin=null, $dependencies=null, $problem=null ) {
  $plugin_name = basename( $plugin, ".php" );
  $dependencies = str_replace( ":", __(" version ", null) , $dependencies );
  $text = "<p><b>";
  $text .= sprintf( __( '%1$s may not be fully functional.', null), $plugin_name );
  $text .= "</b> ";
  $text .= sprintf( __( 'Please install and activate the required minimum version of this plugin: %1$s', null ), $dependencies );
	$text .= "</p>";
  
  if ( current_filter() == "admin_notices" ) {
    $message = '<div class=" updated fade">';
    $message .= $text;
    $depends = strtok( $dependencies, " " );
    $message .= oik_plugin_oik_install_link( $depends, $problem );
    $message .= '</div>'; 
    
  } else {
    $message = '<tr class="plugin-update-tr">';
    $message .= '<td colspan="3" class="plugin-update colspanchange">';
    $message .= '<div class="update-message">';
    $message .= $text;
    $message .= "</div>";
    $message .= "</td>";
    $message .= "</tr>";
  }
  echo $message; 
}
}

/**
 * Test if this plugin is functional
 * 
 * Unless oik is installed and activated this plugin won't do anything
 * Note: If oik is installed and activated then we shouldn't have any problem
 * unless there's a version number mismatch.
*/
function oik_plugin_lazy_activation( $plugin=null, $dependencies=null, $callback=null ) {
  if ( function_exists( "oik_depends" ) ) {  
    /* Good - oik appears to be activated and loaded */
    oik_depends( $plugin, $dependencies, $callback );
  } else {
    call_user_func( $callback, $plugin, $dependencies, "missing" );
  }   
}


/**
 * Simple implementation of plugin dependency logic
 *
 * @param string $plugin - the plugin file name
 * @param string $dependencies - the list of plugins upon which this plugin is dependent
 * @param string $callback - the callback function to invoke when the dependencies aren't satisfied
 *
 * Instead of calling this module during activation we invoke it in response to the 
 * after_plugin_row_$plugin_basename action.
 * 
 * This gives us a bit more control over the information we provide.
 * IF the oik plugin is not activated then oik_lazy_depends() will not be defined
 * 
 */
if ( !function_exists( "oik_depends" ) ) {
function oik_depends( $plugin=null, $dependencies="oik", $callback=null ) {
  //bw_trace2();
  //if ( function_exists( "oik_load_plugins" )) {
  //  oik_load_plugins();
  //}  
  if ( function_exists( "oik_lazy_depends" ) ) {  
    oik_lazy_depends( $plugin, $dependencies, $callback );
  } else {
    if ( is_callable( $callback ) ) {
      call_user_func( $callback, $plugin, $dependencies, "missing" );
    }  
  }  
}
}


} // end else 

} // end if !defined() 
