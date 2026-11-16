<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/Core/helpers.php';
session_start();
$_SESSION['staff_user'] = ['id'=>1,'name'=>'System Administrator','username'=>'admin','role'=>'admin'];
// create unpaid order
$res = App\Models\Order::createWithItems(
  ['customer_name'=>'Test','customer_contact'=>'123','notes'=>''],
  [['product_id'=>1,'quantity'=>1]]
);
$orderId = $res['order_id'];
$tok = csrf_token();
$_SERVER['REQUEST_METHOD']='POST';
$_POST['csrf_token']=$tok;
$_POST['order_id']=$orderId;
$_POST['cash_received']='100';
file_put_contents(__DIR__.'/sid.txt', session_id());
$ctrl = new App\Controllers\Admin\OrderController();
$ctrl->markPaid();
echo "order=$orderId\n";
