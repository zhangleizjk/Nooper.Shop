<?php

// declare(strict_types = 1);
namespace NooperShop;

require_once '../../init/loader.php';

$user = new User('admin', 'Goodman.Saul');
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
// echo $user->unique_id();
// echo strlen($user->unique_id());
// echo $user->unique_collection_id();
// echo strlen($user->unique_collection_id());
// echo $user->get_param(User::PARAM_MAX_USER_NUM);
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
// echo $user->num();
// print_r($user->page());
// print_r($user->record('HNDEE-PQ1KEKGI-DSMNZT2-V3B4K'));
// print_r($user->find('Pinkman'));
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
// echo $user->get_cart_num('P0FYA-OINLQ04B-OT9ASUG-C1FLM');
// print_r($user->get_cart_page('P0FYA-OINLQ04B-OT9ASUG-C1FLM'));
print_r($user->get_cart_record('W8QWRVTT9MYAERMH0HNRN2KACKNEER'));
// echo $user->add_cart('P0FYA-OINLQ04B-OT9ASUG-C1FLM', 8, 12, 2);
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
// echo $user->get_collection_num('P0FYA-OINLQ04B-OT9ASUG-C1FLM');
// print_r($user->get_collection_page('P0FYA-OINLQ04B-OT9ASUG-C1FLM'));
// echo $user->delete_collection('ZWPGV54VC2UXAG5ITNU11EQYTFBAAZ');
// echo $user->add_collection('P0FYA-OINLQ04B-OT9ASUG-C1FLM', 8);
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
// echo $user->get_footmark_num('P0FYA-OINLQ04B-OT9ASUG-C1FLM');
// print_r($user->get_footmark_page('P0FYA-OINLQ04B-OT9ASUG-C1FLM'));
// echo $user->delete_footmark('EEI5UXX3JQW2HBSVACASJZWP06G6NL');
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
// echo $user->get_delivery_address_num('P0FYA-OINLQ04B-OT9ASUG-C1FLM');
// print_r($user->get_delivery_address_page('P0FYA-OINLQ04B-OT9ASUG-C1FLM'));
// print_r($user->get_delivery_address_record('A7QPQ5HL4QCVMZPKXPVZ87FK3OAUGH'));
// echo $user->delete_delivery_address('A7QPQ5HL4QCVMZPKXPVZ87FK3OAUGH');
// echo $user->add_delivery_address('P0FYA-OINLQ04B-OT9ASUG-C1FLM', 1, 5, 99, 1997, 1590, 'China Hebei Chengde Chengde Xiabancheng', 'DiXianJiaYuan 10-5-202', 'White.Walter', '18630856246', '067400');
// echo $user->set_default_delivery_address('SHWKBRSZUQISLTJ3WGYOIEBJCZXFEC');
/* ---------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
// echo $user->get_last_sql();

