/**
 * Working in Nooper_Shop
 */
use `nooper_shop`
 
 
/**
 * Part 1: System
 */
truncate table `system_default_params`;
insert into `system_default_params`(`id`, `money_type`, `exchange_growth_rate`, `exchange_point_rate`, `add_time`) values
		(1, 'CNY', 0.10, 1.00, UNIX_TIMESTAMP());
truncate table `system_currencies`;
insert into `system_currencies`(`id`, `code`, `name`, `last_edit_time`, `add_time`) values
		(1, 'CNY', 'Chinese Yuan', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 'USD', 'United States Dollar', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 'EUR', 'European Dollar', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
truncate table `system_rotators`;
 

/**
 * Part 2: Manager 
 */
truncate table `manager_default_params`;
insert into `manager_default_params`(`id`, `max_manager_num`, `add_time`) values
		(1, 1, UNIX_TIMESTAMP());
truncate table `manager_permissions`;
insert into `manager_permissions`(`id`, `code`, `name`, `description`, `last_edit_time`, `add_time`) values
		(1, 'System', 'System Permission', '商城系统内最高权限，能够对系统进行全面管理，默认授予内置超级管理员角色，-)-', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 'Backup-DataBase', 'Backup DataBase Permission', '商城系统内备份、恢复和重置运行数据库的权限，-)-', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 'Manager', 'Manager Permission', '商城系统内创建、删除和授权管理角色以及管理员的权限，-)-', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, 'User', 'User Permission', '商城系统内管理客户的权限，-)-', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(5, 'Product', 'Product Permission', '商城系统内管理商品的权限，-)-', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(6, 'Order', 'Order Permission', '商城系统内管理订单的权限，-)-', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(7, 'Gift', 'Gift Permission', '商城系统内管理礼物卡的权限，-)-', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(8, 'Coupon', 'Coupon Permission', '商城系统内管理优惠卷的权限，-)-', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(9, 'Message', 'Message Permission', '商城系统内管理消息的权限，-)-', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(10, 'Express', 'Express Permission', '商城系统内管理物流快递的权限，-)-', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(11, 'Tax', 'Tax Permission', '商城系统内管理跨境税费的权限，-)-', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
truncate table `manager_roles`;
insert into `manager_roles`(`id`, `code`, `name`, `description`, `last_edit_time`, `add_time`) values
		(1, 'System-Administrator', 'System Administrator', '商城系统内置超级管理员角色，被授予系统最高权限，能够对系统进行全面管理，-)-', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 'Backup-DataBase-Operator', 'Backup DataBase Operator', '商城系统内置数据库管理员角色，被授予备份、恢复和重置运行数据库的权限， -)-', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 'Operator', 'Operator', '商城系统内置业务管理员角色，被授予完整的业务处理的权限，-)-', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
truncate table `manager_role_permissions`;
insert into `manager_role_permissions`(`id`, `role_id`, `permission_id`, `last_edit_time`, `add_time`) values
		(1, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 2, 2, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 3, 4, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, 3, 5, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(5, 3, 6, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(6, 3, 7, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(7, 3, 8, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),		
		(8, 3, 9, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(9, 3, 10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(10, 3, 11, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
truncate table `managers`;
insert into `managers`(`id`, `role_id`, `name`, `phone`, `pwd`, `last_edit_time`, `add_time`) values
		(1, 1, 'root', '00000000000', SHA2('root$0528', 512), UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
 
 
/**
 * Part 3: User
 */
truncate table `user_default_params`;
insert into `user_default_params`(`id`, `max_user_num`, `max_user_cart_record_num`, `max_user_collection_record_num`, `max_user_footmark_record_num`, `max_user_delivery_address_record_num`, `add_time`) values
		(1, 1000, 100, 100, 500, 10, UNIX_TIMESTAMP());
truncate table `user_carts`;
truncate table `user_collections`;
truncate table `user_footmarks`;
truncate table `user_delivery_addresses`;
truncate table `user_reviews`;
truncate table `users`;
truncate table `user_balance_records`;
 
 
/**
 * Part 4: Product
 */
truncate table `product_default_params`;
insert into `product_default_params`(`id`, `max_product_category_num`, `max_product_category_property_num`, `max_product_group_num`, `max_product_subgroup_num`, `max_product_num`, `max_product_property_enum_num`, `max_product_video_num`, `max_product_picture_num`, `max_product_description_picture_num`, `max_product_stock_num`, `add_time`) values
		(1, 20, 5, 20, 20, 500, 20, 1, 10, 50, 1000, UNIX_TIMESTAMP());
truncate table `product_categories`;
truncate table `product_category_properties`;
truncate table `product_groups`;
truncate table `product_group_details`;
truncate table `product_subgroups`;
truncate table `product_subgroup_details`;
truncate table `product_videos`;
truncate table `product_pictures`;
truncate table `product_description_pictures`;
truncate table `product_property_enums`;
truncate table `product_models`;
truncate table `products`;
 
 
/**
 * Part 5: Order
 */
truncate table `order_default_params`;
truncate table `order_cancel_reasons`;
insert into `order_cancel_reasons`(`id`, `description`, `last_edit_time`, `add_time`) values
		(1, '商品缺货', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, '商品选择或收货信息错误', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, '快递无法送达', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, '重复下单', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(5, '放弃购买', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(6, '其它原因', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
truncate table `order_close_reasons`;
insert into `order_close_reasons`(`id`, `description`, `last_edit_time`, `add_time`) values
		(1, '商品缺货', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, '快递无法送达', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, '超时未完成支付', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, '其它原因', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
truncate table `order_details`;
truncate table `orders`;
truncate table `order_weixin_pay_records`;
truncate table `order_weixin_refund_ask_records`;
truncate table `order_weixin_refund_records`;
truncate table `order_balance_pay_records`;
truncate table `order_balance_refund_records`;
		
		
/**
 * Part 6: Gift
 */
truncate table `gift_default_params`;
insert into `gift_default_params`(`id`, `max_gift_model_num`, `add_time`) values(1, 50, UNIX_TIMESTAMP());
truncate table`gift_categories`;
insert into `gift_categories`(`id`, `code`, `name`, `description`, `last_edit_time`, `add_time`) values
		(1, 'Electronic-Gift-Card', '电子礼物卡', '***', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
truncate table `gift_models`;
insert into `gift_models`(`id`, `category_id`, `code`, `name`, `money_type`, `recharge_money`, `tag_price`, `discount_price`, `status`, `last_edit_time`, `add_time`) values
		(1, 1, '100-egc', '100元电子礼物卡', 'CNY', 100.00, 100.00, 100.00, 'enabled', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 1, '200-egc', '200元电子礼物卡', 'CNY', 200.00, 200.00, 200.00, 'enabled', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 1, '500-egc', '500元电子礼物卡', 'CNY', 500.00, 500.00, 500.00, 'enabled', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, 1, '1000-egc', '1000元电子礼物卡', 'CNY', 1000.00, 1000.00, 1000.00, 'enabled', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(5, 1, '2000-egc', '2000元电子礼物卡', 'CNY', 2000.00, 2000.00, 2000.00, 'enabled', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
truncate table `gifts`;
truncate table `gift_weixin_pay_records`;
 
 
/**
 * Part 7: Coupon
 */
truncate table `coupon_default_params`;
truncate table `coupon_categories`;
insert into `coupon_categories`(`id`, `code`, `name`, `description`, `last_edit_time`, `add_time`) values
		(1, 'Red-Packet', '全部商品红包', '购买全部商品都可以使用的红包，不设最低消费金额，-)-', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 'Full-Range-Coupon', '全部商品满减优惠卷', '购买全部商品都可以使用的优惠卷，设有最低消费金额，订单总金额达到最低消费金额才可以使用，-)-', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
truncate table `coupon_models`;
truncate table `coupons`;


/**
 * Part 8: Message
 */
truncate table `message_default_params`;
insert into `message_default_params`(`id`, `max_read_keep_duration`, `add_time`) values
		(1, 2592000, UNIX_TIMESTAMP());
truncate table `message_categories`;
insert into `message_categories`(`id`, `code`,`name`, `description`, `last_edit_time`, `add_time`) values
		(1, 'Important', '重要消息', '***', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 'Account', '账户消息', '***', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 'Payment', '支付消息', '***', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, 'Express', '物流消息', '***', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(5, 'Discount', '折扣消息', '***', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(6, 'Other', '其它消息', '***', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
truncate table `message_templates`;
insert into `message_templates`(`id`, `category_id`, `code`, `name`, `title`, `description`, `last_edit_time`, `add_time`) values
		(1, 3, 'Order-Payment-Successful', '订单付款成功消息模板', '订单付款成功', '您的订单付款成功（订单编号：<a href="#">[##order_unique_id##]</a>，付款金额：[##pay_money##][##money_type##]）,商品准备出库发货，-）-', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 
		(2, 3, 'Gift-Payment-Successful', '礼品卡付款成功消息模板', '礼品卡付款成功', '***', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 3, 'Order-Refund-Successful', '订单退款成功消息模板', '订单退款成功', '***', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
truncate table `messages`;
		
 
/**
 * Part 9: Express
 */
truncate table `express_default_params`;
insert into `express_default_params`(`id`, `max_carriage_template_num`, `max_corporation_num`, `add_time`) values(1, 50, 20, UNIX_TIMESTAMP());
-- execute file: init_mysql_express_data.sql --
truncate table `express_carriage_templates`;
insert into `express_carriage_templates`(`id`, `code`, `name`, `money_type`, `basic_money`, `progress_money`, `last_edit_time`, `add_time`) values
		(1, 'Basic-Carriage-Template', '基础运费模板', 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
truncate table `express_carriage_template_details`;
insert into `express_carriage_template_details`(`id`, `template_id`, `region_id`, `province_id`, `money_type`, `basic_money`, `progress_money`, `last_edit_time`, `add_time`) values
		(1, 1, 1, 1, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 1, 1, 2, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 1, 1, 3, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, 1, 1, 4, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(5, 1, 1, 5, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(6, 1, 1, 6, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(7, 1, 1, 7, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(8, 1, 1, 8, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(9, 1, 1, 9, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(10, 1, 1, 10, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(11, 1, 1, 11, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(12, 1, 1, 12, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(13, 1, 1, 13, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(14, 1, 1, 14, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(15, 1, 1, 15, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(16, 1, 1, 16, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(17, 1, 1, 17, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(18, 1, 1, 18, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(19, 1, 1, 19, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(20, 1, 1, 20, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(21, 1, 1, 21, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(22, 1, 1, 22, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(23, 1, 1, 23, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(24, 1, 1, 24, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(25, 1, 1, 25, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(26, 1, 1, 26, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(27, 1, 1, 27, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(28, 1, 1, 28, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(29, 1, 1, 29, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(30, 1, 1, 30, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(31, 1, 1, 31, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(32, 1, 1, 32, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(33, 1, 1, 33, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
truncate table `express_corporations`;
insert into `express_corporations`(`id`, `code`, `name`, `home_page`, `is_default`, `last_edit_time`, `add_time`) values
		(1, 'SF-Express', '顺丰快递', 'http://www.sf-express.com', TRUE, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 'STO-Express', '申通快递', 'http://www.sto.cn', FALSE, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 'YTO-Express', '圆通快递', 'http://www.yto.net.cn', FALSE, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, 'ZTO-Express', '中通快递', 'http://www.zto.com', FALSE, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),	
		(5, 'YUNDA-Express', '韵达快递', 'http://www.yundaex.com', FALSE, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(6, 'DEPPON-Express', '德邦快递', 'https://www.deppon.com', FALSE, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(7, 'ZJS-Express', '宅急送', 'http://www.zjs.com.cn', FALSE, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(8, 'EMS-Express', 'EMS', 'http://www.ems.com.cn', FALSE, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());	
truncate table `expresses`;
		
		
/**
 * Part 10: Tax
 */
truncate table `tax_default_params`;
truncate table `taxes`;













