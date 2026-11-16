<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/Core/helpers.php';
session_start();
$_SESSION['staff_user'] = ['id'=>1,'name'=>'System Administrator','username'=>'admin','role'=>'admin'];
$tok = csrf_token();
$pdo = App\Core\Database::connection();
$unpaid = (int)$pdo->query("SELECT id FROM orders WHERE payment_status='unpaid' ORDER BY id DESC LIMIT 1")->fetchColumn();
$_SERVER['REQUEST_METHOD']='POST';
$_POST['csrf_token']=$tok;
$_POST['order_id']=$unpaid;
$_POST['cash_received']='50';
$sid = session_id();
file_put_contents(__DIR__.'/sid.txt', $sid);
$ctrl = new App\Controllers\Admin\OrderController();
$ctrl->markPaid();
echo "done\n";
