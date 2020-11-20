<?php

global $wpdb;
$tablename = $wpdb->prefix."posts";
$tablename2 = $wpdb->prefix."postmeta";

//Export Table to CSV
if (isset($_POST['Export'])) {

    ob_get_clean();

    $domain = $_SERVER['SERVER_NAME'];
    $filename = '99xOrder-' . $domain . '-' . time() . '.csv';

    $header_row = array(
        'ID',
        'Date',
        'Customer Name',
        'Order Status',
        'Total',
    );
    $data_rows = array();

    $sql = "SELECT p.ID, p.post_date, p.post_status,pm.meta_value as name, pm2.meta_value as total, pm3.meta_value as currency FROM ".$tablename." p
    INNER JOIN ".$tablename2." pm ON p.ID = pm.post_id
INNER JOIN gbplol_postmeta pm2
  ON p.ID = pm2.post_id
INNER JOIN gbplol_postmeta pm3
  ON p.ID = pm3.post_id
    WHERE p.post_type = 'shop_order'
    AND pm.meta_key = '_billing_first_name'
AND pm2.meta_key='_order_total'
AND pm3.meta_key='_order_currency'";
    $orders = $wpdb->get_results($sql, 'ARRAY_A');
    foreach ($orders as $order) {
        $row = array(
            $order['ID'],
            $order['post_date'],
            $order['name'],
            $order['post_status'],
            $order['total'],
        );
        $data_rows[] = $row;
    }

    $fh = @fopen('php://output', 'w');
    fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Description: File Transfer');
    header('Content-type: text/csv');
    header("Content-Disposition: attachment; filename={$filename}");
    header('Expires: 0');
    header('Pragma: public');
    fputcsv($fh, $header_row);
    foreach ($data_rows as $data_row) {
        fputcsv($fh, $data_row);
    }
    fclose($fh);

    ob_end_flush();

    die();
}
?>

<!-- DISPLAY ORDERS -->
<h1>All Orders</h1>

<table width='100%' border='1' style='border-collapse: collapse;'>
  <tr>
   <th>Order Number</th>
   <th>Date</th>
   <th>Customer Name</th>
   <th>Order Status</th>
   <th>Order Total</th>
  </tr>
  <?php
// Select records
$entriesList = $wpdb->get_results("SELECT p.ID, p.post_date, p.post_status,pm.meta_value as name, pm2.meta_value as total, pm3.meta_value as currency FROM ".$tablename." p
    INNER JOIN ".$tablename2." pm ON p.ID = pm.post_id
INNER JOIN gbplol_postmeta pm2
  ON p.ID = pm2.post_id
INNER JOIN gbplol_postmeta pm3
  ON p.ID = pm3.post_id
    WHERE p.post_type = 'shop_order'
    AND pm.meta_key = '_billing_first_name'
AND pm2.meta_key='_order_total'
AND pm3.meta_key='_order_currency'");
if (count($entriesList) > 0) {
    $count = 1;
    foreach ($entriesList as $entry) {
        $id = $entry->ID;
        $date = $entry->post_date;
        $name = $entry->name;
        $status = $entry->post_status;
        $total = $entry->total;
        $currency = $entry->currency;

        echo "<tr>
      <td>" . $id . "</td>
      <td>" . $date . "</td>
      <td>" . $name . "</td>
      <td>" . $status . "</td>
      <td>" . $total . " " . $currency . "</td>
      </tr>
      ";
        $count++;
    }
} else {
    echo "<tr><td colspan='5'>No record found</td></tr>";
}
?>
</table>
<div><form class="form-horizontal" action="" enctype="multipart/form-data" method="post" name="upload_excel">
    <hr/>
    <input class="btn btn-success" name="Export" type="submit" value="export to excel" />
    </form>
<!-- DISPLAY ORDERS -->    