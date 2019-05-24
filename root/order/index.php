<?php

// declare(strict_types = 1);
namespace NooperShop;

require_once '../../init/loader.php';

$order = new Order('admin', 'iroul');
$p=new Product('admin', 'iroul');
echo $p->unique_id();
echo "\n\r";
echo $p->unique_picture_file_name();
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
echo $order->unique_id();
 echo strlen($order->unique_id());
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
// echo $order->ship(1, 1, '906838329318', 12.00, 'Order Shipping, -)-');

/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
$details=[
		[
			'product_id'=>1,
			'product_detail_id'=>23,
			'product_unique_id'=>'UIQJN-JNGOFL-7IL-ANNKZ',
			'product_code'=>'ThinkPad-X280-20KFA01PCD',
			'product_name'=>'ThinkPad X280 20KFA01PCD',
			'product_property_enum_data_group'=> 'CPU**Core i5-8250U##HDD**512GB##Color**Black',
			'tag_price'=>6799.00,
			'discount_price'=>6799.00,
			'quantity'=>2
		]
];
// echo $order->create(1, 12598.00, 12598.00, 1200.00, 280.00, 200.00, 13878.00, 'ShuangGang town, JinNan district, TianJin, China', 'Goodwin.BigHorse', '18630856256', $details, 387, '300350', 'Must SF-Express.');
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
// print_r($order->get_order_user_datas('PUHBKN15-0ZSWZ-J30F6LQM-EX9USKJ5'));
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------- */

//  echo $order->get_last_sql();
