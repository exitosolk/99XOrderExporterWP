<?php
/*
   Plugin Name: 99x-OrderExporter plugin
   Plugin URI: https://chandu.xyz
   description: 99X Assignment
   Version: 1.0.0
   Author: Supun Chandula Illeperuma
   Author URI: https://chandu.xyz/
*/


// Add menu
function exportOrder_menu() {

    add_menu_page("99X-OrderExporter Plugin", "99X-OrderExporter Plugin","manage_options", "myplugin", "displayList",plugins_url('/99X-OrderExporter/img/icon.png'));
    add_submenu_page("myplugin","All Orders", "All Orders","manage_options", "allentries", "displayOrders");
}
add_action("admin_menu", "exportOrder_menu");

function displayOrders(){
  include "display_orders.php";
}

