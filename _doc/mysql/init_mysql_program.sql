/**
 * Configuring Global Params
 */
set global event_scheduler=true;


/**
 * Working in Nooper_Shop
 */
use `nooper_shop`
 
 
/**
 * Part 1: System
 */
 
 
/**
 * Part 2: Manager
 */
 
 
/**
 * Part 3: User
 */
 
 
 /** 
 * Part 4: Product
 */

 
/**
 * Part 5: Order
 */


 /**
 * Event: Close_Unpaid_Order
 */
drop event if exists close_unpaid_order;
delimiter **
create event if not exists close_unpaid_order
	on schedule every 1 hour
		starts CURDATE()
	on completion preserve
	enable
	do
		begin
			 declare max_duration bigint;
			 set max_duration=60*60*24*2;
			update `orders` set `status`='closed', `close_reason`='订单超时未完成支付', `close_time`=UNIX_TIMESTAMP(), `close_manager`='root', `last_edit_time`=UNIX_TIMESTAMP() 
				where `status`='unpaid' and (UNIX_TIMESTAMP()-`add_time`>max_duration);
		end**
delimiter ;
 
 
/**
 * Part 6: Gift
 */
 
 
/**
 * Event: Clear_Unpaid_Gift
 */
drop event if exists clear_unpaid_gift;
delimiter **
create event if not exists clear_unpaid_gift
	on schedule every 1 hour
		starts CURDATE()
	on completion preserve
	enable
	do
		begin
			 declare max_duration bigint;
			 set max_duration=60*60*24*2;
			delete from `gifts` where `status`='unpaid' and (UNIX_TIMESTAMP()-`add_time`>max_duration);
		end**
delimiter ;
 
 
/**
 * Part 7: Coupon
 */
 
 
/**
  * Event: Refresh_Expired_Coupon
  */
drop event if exists refresh_expired_coupon;
delimiter **
create event if not exists refresh_expired_coupon
	on schedule every 1 hour
		starts CURDATE()
	on completion preserve
	enable
	do
		begin
			update `coupon_models` set `status`='expired', `last_edit_time`=UNIX_TIMESTAMP() where (`status` in ('enabled', 'disabled')) and (UNIX_TIMESTAMP()>`end_time`);
			update `coupons` set `status`='expired', `last_edit_time`=UNIX_TIMESTAMP() where `status`='unused' and (UNIX_TIMESTAMP()>`end_time`);
		end**
delimiter ;


/**
 * Event: Clear_Expired_Coupon
 */
drop event if exists clear_expired_coupon;
delimiter ** 
create event if not exists clear_expired_coupon
	on schedule every 1 hour
		starts CURDATE()
	on completion preserve
	enable
	do
		begin
			 declare max_duration bigint;
			 set max_duration=3600*24*30;
			delete from `coupons` where `status`='expired' and (UNIX_TIMESTAMP()-`end_time`>max_duration);
		end**
delimiter ;
 
 
/**
 * Part 8: Message
 */
 
 
/**
 * Event: Clear_Read_Message
 */
drop event if exists clear_read_message;
delimiter ** 
create event if not exists clear_read_message
	on schedule every 1 hour
		starts CURDATE()
	on completion preserve
	enable
	do
		begin
			 declare max_duration bigint;
			 set max_duration=60*60*24*30;
			delete from `messages` where `status`='read' and (UNIX_TIMESTAMP()-`read_time`>max_duration);
		end**
delimiter ;
 
 
/**
 * Part 9: Express
 */
 
 
/**
 * Part 10: Tax
 */



