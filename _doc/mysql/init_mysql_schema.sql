/**
 * Schema: Nooper_Shop
 */
drop schema if exists `nooper_shop`;
create schema if not exists `nooper_shop`
	default character set utf8mb4
	default collate utf8mb4_bin;

	
/**
 * Working in Nooper_Shop
 */
use `nooper_shop`


/**
 * Part 1: System
 */
 
 
/**
 * Table: System_Default_Params
 */
drop table if exists `system_default_params`;
create table if not exists `system_default_params`
(
	`id` bigint unsigned auto_increment not null,
	`money_type` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`exchange_growth_rate` decimal(3, 2) unsigned not null,
	`exchange_point_rate` decimal(3, 2) unsigned not null,
	`add_time` bigint unsigned not null,
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;
	
	
/**
 * Table: System_Currencies
 */
drop table if exists `system_currencies`;
create table if not exists `system_currencies`
(
	`id` bigint unsigned auto_increment not null,
	`code` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	unique(`code`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;

	
/**
 * Table: System_Rotators
 */
drop table if exists `system_rotators`;
create table if not exists `system_rotators`
(
	`id` bigint unsigned auto_increment not null,
	`file_name` varchar(50) character set utf8mb4 collate utf8mb4_bin not null,
	`place` int unsigned default 0,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	unique(`file_name`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Part 2: Manager
 */
 
 
/**
 * Table: Manager_Default_Params
 */
drop table if exists `manager_default_params`;
create table if not exists `manager_default_params`(
	`id` bigint unsigned auto_increment not null,
	`max_manager_num` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;	
	

/**
 * Table: Manager_Permissions
 */
drop table if exists `manager_permissions`;
create table if not exists `manager_permissions`(
	`id` bigint unsigned auto_increment not null,
	`code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`description` varchar(200) character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	unique(`code`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;
	

/**
 * Table: Manager_Roles
 */
drop table if exists `manager_roles`;
create table if not exists `manager_roles`(
	`id` bigint unsigned auto_increment not null,
	`code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`description` varchar(200) character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	unique(`code`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;

	
/**
 * Table: Manager_Role_Permissions
 */
drop table if exists `manager_role_permissions`;
create table if not exists `manager_role_permissions`(
	`id` bigint unsigned auto_increment not null,
	`role_id` bigint unsigned not null,
	`permission_id` bigint unsigned not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`role_id`) references `manager_roles`(`id`),
	-- foreign key(`permission_id`) references `manager_permissions`(`id`),
	unique(`role_id`, `permission_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;
	
	
/**
 * Table: Managers
 */
drop table if exists `managers`;
create table if not exists `managers`
(
	`id` bigint unsigned auto_increment not null,
	`role_id` bigint unsigned not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`phone` varchar(20) character set utf8mb4 collate utf8mb4_bin not null,
	`pwd` char(128) character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`role_id`) references `manager_roles`(`id`),
	unique(`name`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;

	
/**
 * Part 3: User
 */
 
 
/**
 * Table: User_Default_Params
 */
drop table if exists `user_default_params`;
create table if not exists `user_default_params`
(
	`id` bigint unsigned auto_increment not null,
	`max_user_num` bigint unsigned not null,
	`max_user_cart_record_num` bigint unsigned not null,
	`max_user_collection_record_num` bigint unsigned not null,
	`max_user_footmark_record_num` bigint unsigned not null,
	`max_user_delivery_address_record_num` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Table: User_Carts
 */
drop table if exists `user_carts`;
create table if not exists `user_carts`
(
	`id` bigint unsigned auto_increment not null,
	`unique_id` char(30) character set utf8mb4 collate utf8mb4_bin not null,
	`user_id` bigint unsigned not null,
	`product_id` bigint unsigned null,
	`product_model_id` bigint unsigned null,
	`product_unique_id` char(22) character set utf8mb4 collate utf8mb4_bin not null,
	`product_code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`product_name` varchar(100) character set utf8mb4 collate utf8mb4_bin not null,
	`product_model_description` varchar(120) character set utf8mb4 collate utf8mb4_bin not null,
	`quantity` int unsigned not null,
	`status` enum('online', 'offline', 'deleted', 'no-model', 'no-enough') character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`user_id`) references `users`(`id`),
	-- foreign key(`product_id`) references `products`(`id`) on delete set null,
	-- foreign key(`product_model_id`) references `product_models`(`id`) on delete set null,
	unique(`unique_id`),
	unique(`user_id`, `product_model_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Table: User_Collections
 */
drop table if exists `user_collections`;
create table if not exists `user_collections`
(
	`id` bigint unsigned auto_increment not null,
	`unique_id` char(30) character set utf8mb4 collate utf8mb4_bin not null,
	`user_id` bigint unsigned not null,
	`product_id` bigint unsigned null,
	`product_unique_id` char(22) character set utf8mb4 collate utf8mb4_bin not null,
	`product_code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`product_name` varchar(100) character set utf8mb4 collate utf8mb4_bin not null,
	`status` enum('online', 'offline', 'deleted') character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`user_id`) references `users`(`id`),
	-- foreign key(`product_id`) references `products`(`id`) on delete set null,
	unique(`unique_id`),
	unique(`user_id`,`product_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;

	
/**
 * Table: User_Footmarks
 */
drop table if exists `user_footmarks`;
create table if not exists `user_footmarks`
(
	`id` bigint unsigned auto_increment not null,
	`unique_id` char(30) character set utf8mb4 collate utf8mb4_bin not null,
	`user_id` bigint unsigned not null,
	`product_id` bigint unsigned null,
	`product_unique_id` char(22) character set utf8mb4 collate utf8mb4_bin not null,
	`product_code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`product_name` varchar(100) character set utf8mb4 collate utf8mb4_bin not null,
	`status` enum('online', 'offline', 'deleted') character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`user_id`) references `users`(`id`),
	-- foreign key(`product_id`) references `products`(`id`) on delete set null,
	unique(`unique_id`),
	unique(`user_id`, `product_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;

	
/**
 * Table: User_Delivery_Addresses
 */
drop table if exists `user_delivery_addresses`;
create table if not exists `user_delivery_addresses`
(
	`id` bigint unsigned auto_increment not null,
	`unique_id` char(30) character set utf8mb4 collate utf8mb4_bin not null,
	`user_id` bigint unsigned not null,
	`express_address_region_id` bigint unsigned not null,
	`express_address_province_id` bigint unsigned null,
	`express_address_city_id` bigint unsigned null,
	`express_address_county_id` bigint unsigned null,
	`express_address_town_id` bigint unsigned null,
	`primary_address` varchar(100) character set utf8mb4 collate utf8mb4_bin not null,
	`detail_address` varchar(100) character set utf8mb4 collate utf8mb4_bin not null,	
	`receiver` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`phone` varchar(20) character set utf8mb4 collate utf8mb4_bin not null,
	`postcode` varchar(20) character set utf8mb4 collate utf8mb4_bin null,
	`is_default` boolean default 0,
	`status` enum('enabled', 'disabled') character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`user_id`) references `users`(`id`),
	-- foreign key(`express_address_region_id`) references `express_address_regions`(`id`),
	-- foreign key(`express_address_province_id`) references `express_address_provinces`(`id`),
	-- foreign key(`express_address_city_id`) references `express_address_cities`(`id`),
	-- foreign key(`express_address_county_id`) references `express_address_counties`(`id`),
	-- foreign key(`express_address_town_id`) references `express_address_towns`(`id`),
	unique(`unique_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Table: User_Reviews
 */
drop table if exists `user_reviews`;
create table if not exists `user_reviews`
(
	`id` bigint unsigned auto_increment not null,
	`unique_id` char(30) character set utf8mb4 collate utf8mb4_bin not null,
	`user_id` bigint unsigned not null,
	`order_id` bigint unsigned not null,
	`product_id` bigint unsigned null,
	`product_model_id` bigint unsigned null,
	`product_unique_id` char(22) character set utf8mb4 collate utf8mb4_bin not null,
	`product_code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`product_name` varchar(100) character set utf8mb4 collate utf8mb4_bin not null,
	`product_model_description` varchar(120) character set utf8mb4 collate utf8mb4_bin not null,
	`grade` enum('5', '4', '3', '2', '1') character set utf8mb4 collate utf8mb4_bin not null,
	`description` varchar(1000) character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`user_id`) references `users`(`id`),
	-- foreign key(`order_id`) references `orders`(`id`),
	-- foreign key(`product_id`) references `products`(`id`) on delete set null,
	-- foreign key(`product_model_id`) references `product_models`(`id`) on delete set null,
	unique(`unique_id`),
	unique(`order_id`,`product_model_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;

	
/**
 * Table: Users
 */
drop table if exists `users`;
create table if not exists `users`
(
	`id` bigint unsigned auto_increment not null,
	`unique_id` char(28) character set utf8mb4 collate utf8mb4_bin not null,
	`open_id` varchar(128) character set utf8mb4 collate utf8mb4_bin not null,
	`nickname` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`sex` enum('secrecy', 'male', 'female') character set utf8mb4 collate utf8mb4_bin not null,
	`head_img_url` varchar(200) character set utf8mb4 collate utf8mb4_bin null,
	`real_name` varchar(40) character set utf8mb4 collate utf8mb4_bin null,
	`phone` varchar(20) character set utf8mb4 collate utf8mb4_bin null,
	`pwd` char(128) character set utf8mb4 collate utf8mb4_bin null,
	`balance` decimal(10, 2) unsigned default 0.00,
	`growth` bigint unsigned default 0,
	`point` bigint unsigned default 0,
	`status` enum('unregistered', 'registered', 'locked') character set utf8mb4 collate utf8mb4_bin not null,	
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	unique(`unique_id`),
	unique(`open_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;	
	
	
/**
 * Table: User_Balance_Records
 */
drop table if exists `user_balance_records`;
create table if not exists `user_balance_records`
(
	`id` bigint unsigned auto_increment not null,
	`user_id` bigint unsigned not null,
	`money_type` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`change_money` decimal(10, 2) not null,
	`description` varchar(100) character set utf8mb4 collate utf8mb4_bin not null,
	`operate` enum('pay', 'refund', 'recharge', 'other') character set utf8mb4 collate utf8mb4_bin not null,
	`operate_manager` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`user_id`) references `users`(`id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;
	
	
/**
 * Part 4: Product
 */
 

/**
 * Table: Product_Default_Params
 */
drop table if exists `product_default_params`;
create table if not exists `product_default_params`(
	`id` bigint unsigned auto_increment not null,
	`max_product_category_num` int unsigned not null,
	`max_product_category_property_num` int unsigned not null,
	`max_product_group_num` int unsigned not null,
	`max_product_subgroup_num` int unsigned not null,
	`max_product_num` int unsigned not null,
	`max_product_property_enum_num` int unsigned not null,
	`max_product_video_num` int unsigned not null,
	`max_product_picture_num` int unsigned not null,
	`max_product_description_picture_num` int unsigned not null,
	`max_product_stock_num` int unsigned not null,
	`add_time` bigint unsigned not null,
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;		

 
/**
 * Table: Product_Categories
 */
drop table if exists `product_categories`;
create table if not exists `product_categories`
(
	`id` bigint unsigned auto_increment not null,
	`code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`place` int unsigned default 0,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	unique(`code`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;

 
/**
 * Table: Product_Category_Properties
 */
 drop table if exists `product_category_properties`;
create table if not exists `product_category_properties`
(
	`id` bigint unsigned auto_increment not null,
	`category_id` bigint unsigned not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`category_id`) references `product_categories`(`id`),
	primary key(`id`)
)
engine innodb
default character set utf8mb4
default collate utf8mb4_bin;


/**
 * Table: Product_Groups
 */
drop table if exists `product_groups`;
create table if not exists `product_groups`(
	`id` bigint unsigned auto_increment not null,
	`code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`place` int unsigned default 0,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	unique(`code`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;	
	
	
/**
 * Table: Product_Group_Details
 */
drop table if exists `product_group_details`;
create table if not exists `product_group_details`(
	`id` bigint unsigned auto_increment not null,
	`group_id` bigint unsigned not null,
	`product_id` bigint unsigned not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`group_id`) references `product_groups`(`id`),
	-- foreign key(`product_id`) references `products`(`id`),
	unique(`group_id`, `product_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;
	
	
/**
 * Table: Product_Subgroups
 */
drop table if exists `product_subgroups`;
create table if not exists `product_subgroups`(
	`id` bigint unsigned auto_increment not null,
	`group_id` bigint unsigned not null,
	`code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`place` int unsigned default 0,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`group_id`) references `product_groups`(`id`),
	unique(`group_id`, `code`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;	
	
	
/**
 * Table: Product_Subgroup_Details
 */
drop table if exists `product_subgroup_details`;
create table if not exists `product_subgroup_details`(
	`id` bigint unsigned auto_increment not null,
	`group_id` bigint unsigned not null,
	`subgroup_id` bigint unsigned not null,
	`product_id` bigint unsigned not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`group_id`) references `product_groups`(`id`),
	-- foreign key(`subgroup_id`) references `product_subgroups`(`id`),
	-- foreign key(`product_id`) references `products`(`id`),
	unique(`subgroup_id`, `product_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Table: Product_Videos
 */
drop table if exists `product_videos`;
create table if not exists `product_videos`(
	`id` bigint unsigned auto_increment not null,
	`product_id` bigint unsigned not null,
	`file_name` varchar(50) character set utf8mb4 collate utf8mb4_bin not null,
	`place` int unsigned default 0,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`product_id`) references `products`(`id`),
	unique(`file_name`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;		
	

/**
 * Table: Product_Pictures
 */
drop table if exists `product_pictures`;
create table if not exists `product_pictures`(
	`id` bigint unsigned auto_increment not null,
	`product_id` bigint unsigned not null,
	`file_name` varchar(50) character set utf8mb4 collate utf8mb4_bin not null,
	`place` int unsigned default 0,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`product_id`) references `products`(`id`),
	unique(`file_name`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;	
	
	
/**
 * Table: Product_Description_Pictures
 */
drop table if exists `product_description_pictures`;
create table if not exists `product_description_pictures`(
	`id` bigint unsigned auto_increment not null,
	`product_id` bigint unsigned not null,
	`file_name` varchar(50) character set utf8mb4 collate utf8mb4_bin not null,
	`place` int unsigned default 0,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`product_id`) references `products`(`id`),
	unique(`file_name`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;	
	

/**
 * Table: Product_Property_Enums
 */
drop table if exists `product_property_enums`;
create table if not exists `product_property_enums`
(
	`id` bigint unsigned auto_increment not null,
	`product_id` bigint unsigned not null,
	`property_id` bigint unsigned not null,
	`data` varchar(50) character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`product_id`) references `products`(`id`),
	-- foreign key(`property_id`) references `product_category_properties`(`id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Table: Product_Models
 */
drop table if exists `product_models`;
create table if not exists `product_models`(
	`id` bigint unsigned auto_increment not null,
	`product_id` bigint unsigned not null,
	`description` varchar(120) character set utf8mb4 collate utf8mb4_bin not null,
	`money_type` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`tag_price` decimal(10, 2) unsigned not null,
	`discount_price` decimal(10, 2) unsigned not null,
	`stock_num` int unsigned not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`product_id`) references `products`(`id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;		
	

/**
 * Table: Products
 */
drop table if exists `products`;
create table if not exists `products`(
	`id` bigint unsigned auto_increment not null,
	`unique_id` char(22) character set utf8mb4 collate utf8mb4_bin not null,
	`category_id` bigint unsigned not null,
	`express_carriage_template_id` bigint unsigned null,
	`code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`name` varchar(100) character set utf8mb4 collate utf8mb4_bin not null,
	`primary_video` varchar(50) character set utf8mb4 collate utf8mb4_bin null,
	`primary_picture` varchar(50) character set utf8mb4 collate utf8mb4_bin null,
	`money_type` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`min_tag_price` decimal(10, 2) unsigned not null,
	`min_discount_price` decimal(10, 2) unsigned not null,
	`place` int unsigned default 0,
	`click_num` bigint unsigned default 0,
	`sale_num` bigint unsigned default 0,
	`status` enum('prepared', 'online', 'offline') character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`category_id`) references `product_categories`(`id`),
	-- foreign key(`express_carriage_template_id`) references `express_carriage_templates`(`id`) on delete set null,
	unique(`unique_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;		

 
/**
 * Part 5: Order
 */
 
 
/**
 * Table: Order_Default_Params
 */
drop table if exists `order_default_params`;
create table if not exists `order_default_params`
(
	`id` bigint unsigned auto_increment not null,
	`add_time` bigint unsigned not null,
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Table: Order_Cancel_Reasons
 */
drop table if exists `order_cancel_reasons`;
create table if not exists `order_cancel_reasons`
(
	`id` bigint unsigned auto_increment not null,
	`description` varchar(100) character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Table: Order_Close_Reasons
 */
drop table if exists `order_close_reasons`;
create table if not exists `order_close_reasons`
(
	`id` bigint unsigned auto_increment not null,
	`description` varchar(100) character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Table: Order_Details
 */
drop table if exists `order_details`;
create table if not exists `order_details`
(
	`id` bigint unsigned auto_increment not null,
	`order_id` bigint unsigned not null,
	`product_id` bigint unsigned null,
	`product_model_id` bigint unsigned null,
	`product_unique_id` char(22) character set utf8mb4 collate utf8mb4_bin not null,
	`product_code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`product_name` varchar(100) character set utf8mb4 collate utf8mb4_bin not null,
	`product_model_description` varchar(120) character set utf8mb4 collate utf8mb4_bin not null,
	`money_type` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`tag_price` decimal(10, 2) unsigned not null,
	`discount_price` decimal(10, 2) unsigned not null,
	`quantity` int unsigned not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`order_id`) references `orders`(`id`),
	-- foreign key(`product_id`) references `products`(`id`) on delete set null,
	-- foreign key(`product_model_id`) references `product_models`(`id`) on delete set null,
	unique(`order_id`, `product_model_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Table: Orders
 */
drop table if exists `orders`;
create table if not exists `orders`
(
	`id` bigint unsigned auto_increment not null,
	`unique_id` char(32) character set utf8mb4 collate utf8mb4_bin not null,
	`user_id` bigint unsigned not null,
	`coupon_id` bigint unsigned null,
	`money_type` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`total_product_tag_money` decimal(10, 2) unsigned not null,
	`total_product_discount_money` decimal(10, 2) unsigned not null,
	`total_tax_money` decimal(10, 2) unsigned not null,
	`total_express_carriage_money` decimal(10, 2) unsigned not null,
	`total_coupon_discount_money` decimal(10, 2) unsigned not null,
	`pay_money` decimal(10, 2) unsigned not null,
	`address` varchar(200) character set utf8mb4 collate utf8mb4_bin not null,
	`receiver` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`phone` varchar(20) character set utf8mb4 collate utf8mb4_bin not null,
	`postcode` varchar(20) character set utf8mb4 collate utf8mb4_bin null,
	`note` varchar(200) character set utf8mb4 collate utf8mb4_bin null,
	`pay_method` enum('weixin', 'balance') character set utf8mb4 collate utf8mb4_bin null,
	`pay_time` bigint unsigned null,
	`ship_time` bigint unsigned null,
	`complete_time` bigint unsigned null,
	`review_time` bigint unsigned null,
	`cancel_reason` varchar(100) character set utf8mb4 collate utf8mb4_bin null,
	`cancel_time` bigint unsigned null,
	`close_reason` varchar(100) character set utf8mb4 collate utf8mb4_bin null,
	`close_time` bigint unsigned null,
	`close_manager` varchar(40) character set utf8mb4 collate utf8mb4_bin null,
	`status` enum('unpaid', 'paid', 'shipped', 'completed', 'reviewed', 'cancelled', 'closed') character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`user_id`) references `users`(`id`),
	-- foreign key(`coupon_id`) references `coupons`(`id`),
	unique(`unique_id`),
	unique(`coupon_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Table: Order_Weixin_Pay_Records
 */
drop table if exists `order_weixin_pay_records`;
create table if not exists `order_weixin_pay_records`
(
	`id` bigint unsigned auto_increment not null,
	`notify_result_code` varchar(16) character set utf8mb4 collate utf8mb4_bin not null,
	`notify_transaction_id` varchar(32) character set utf8mb4 collate utf8mb4_bin not null,
	`notify_out_trade_no` varchar(32) character set utf8mb4 collate utf8mb4_bin not null,
	`notify_openid` varchar(128) character set utf8mb4 collate utf8mb4_bin not null,
	`notify_fee_type` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`notify_total_fee` decimal(10, 2) unsigned not null,
	`notify_time_end` bigint unsigned not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	unique(`notify_transaction_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Table: Order_Weixin_Refund_Ask_Records
 */
drop table if exists `order_weixin_refund_ask_records`;
create table if not exists `order_weixin_refund_ask_records`
(
	`id` bigint unsigned auto_increment not null,
	`result_code` varchar(16) character set utf8mb4 collate utf8mb4_bin not null,
	`refund_id` varchar(32) character set utf8mb4 collate utf8mb4_bin not null,
	`out_refund_no` varchar(64) character set utf8mb4 collate utf8mb4_bin not null,
	`out_trade_no` varchar(32) character set utf8mb4 collate utf8mb4_bin not null,
	`refund_fee_type` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`total_fee` decimal(10, 2) unsigned not null,
	`refund_fee` decimal(10, 2) unsigned not null,
	`refund_desc` varchar(80) character set utf8mb4 collate utf8mb4_bin not null,
	`refund_manager` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Table: Order_Weixin_Refund_Records
 */
drop table if exists `order_weixin_refund_records`;
create table if not exists `order_weixin_refund_records`
(
	`id` bigint unsigned auto_increment not null,
	`notify_refund_status` varchar(16) character set utf8mb4 collate utf8mb4_bin not null,
	`notify_refund_id` varchar(32) character set utf8mb4 collate utf8mb4_bin not null,
	`notify_out_refund_no` varchar(64) character set utf8mb4 collate utf8mb4_bin not null,
	`notify_out_trade_no` varchar(32) character set utf8mb4 collate utf8mb4_bin not null,
	`notify_total_fee` decimal(10, 2) unsigned not null,
	`notify_refund_fee` decimal(10, 2) unsigned not null,
	`notify_success_time` bigint unsigned null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	unique(`notify_refund_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Table: Order_Balance_Pay_Records
 */
drop table if exists `order_balance_pay_records`;
create table if not exists `order_balance_pay_records`
(
	`id` bigint unsigned auto_increment not null,
	`order_id` bigint unsigned not null,
	`money_type`char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`pay_money` decimal(10, 2) unsigned not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`order_id`) references `orders`(`id`),
	unique(`order_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Table: Order_Balance_Refund_Records
 */
drop table if exists `order_balance_refund_records`;
create table if not exists `order_balance_refund_records`
(
	`id` bigint unsigned auto_increment not null,
	`order_id` bigint unsigned not null,
	`money_type` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`refund_money` decimal(10, 2) unsigned not null,
	`refund_manager` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`order_id`) references `orders`(`id`),
	unique(`order_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;
	

/**
 * Part 6: Gift
 */
 
 
/**
 * Table: Gift_Default_Params
 */
drop table if exists `gift_default_params`;
create table if not exists `gift_default_params`(
	`id` bigint unsigned auto_increment not null,
	`max_gift_model_num` int unsigned not null,
	`add_time` bigint unsigned not null,
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;	
 
 
/**
 * Table: Gift_Categories
 */
drop table if exists `gift_categories`;
create table if not exists `gift_categories`(
	`id` bigint unsigned auto_increment not null,
	`code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`description` varchar(200) character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	unique(`code`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;
	
	
/**
 * Table: Gift_Models
 */
drop table if exists `gift_models`;
create table if not exists `gift_models`(
	`id` bigint unsigned auto_increment not null,
	`category_id` bigint unsigned not null,
	`code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`money_type` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`recharge_money` decimal(10, 2) unsigned not null,
	`tag_price`decimal(10, 2) unsigned not null,
	`discount_price`decimal(10, 2) unsigned not null,
	`status` enum('enabled', 'disabled') character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`category_id`) references `gift_categories`(`id`),
	unique(`code`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;		
	
	
/**
 * Table: Gifts
 */
drop table if exists `gifts`;
create table if not exists `gifts`(
	`id` bigint unsigned auto_increment not null,
	`unique_id` char(30) character set utf8mb4 collate utf8mb4_bin not null,
	`category_id` bigint unsigned not null,
	`model_id` bigint unsigned not null,
	`user_id` bigint unsigned not null,
	`code` char(30) character set utf8mb4 collate utf8mb4_bin not null,
	`money_type` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`recharge_money` decimal(10, 2) unsigned not null,
	`pay_money` decimal(10, 2) unsigned not null,
	`pay_method` enum('weixin') character set utf8mb4 collate utf8mb4_bin null,
	`pay_time` bigint unsigned null,
	`recharge_time` bigint unsigned null,
	`status` enum('unpaid', 'paid', 'recharged') character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null, 
	-- foreign key(`category_id`) references `gift_categories`(`id`),
	-- foreign key(`model_id`) references `gift_models`(`id`),
	-- foreign key(`user_id`) references `users`(`id`),
	unique(`unique_id`),
	unique(`code`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;		
	
	
/**
 * Table: Gift_Weixin_Pay_Records
 */
drop table if exists `gift_weixin_pay_records`;
create table if not exists `gift_weixin_pay_records`(
	`id` bigint unsigned auto_increment not null,
	`notify_result_code` varchar(16) character set utf8mb4 collate utf8mb4_bin not null,
	`notify_transaction_id` varchar(32) character set utf8mb4 collate utf8mb4_bin not null,
	`notify_out_trade_no` varchar(32) character set utf8mb4 collate utf8mb4_bin not null,
	`notify_openid` varchar(128) character set utf8mb4 collate utf8mb4_bin not null,
	`notify_fee_type` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`notify_total_fee` decimal(10, 2) unsigned not null,
	`notify_time_end` bigint unsigned not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	unique(`notify_transaction_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Part 7: Coupon
 */
 
 
/**
 * Table: Coupon_Default_Params
 */
drop table if exists `coupon_default_params`;
create table if not exists `coupon_default_params`(
	`id` bigint unsigned auto_increment not null,
	`add_time` bigint unsigned not null,
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;	

	
/**
 * Table: Coupon_Categories
 */
drop table if exists `coupon_categories`;
create table if not exists `coupon_categories`(
	`id` bigint unsigned auto_increment not null,
	`code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`description` varchar(200) character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	unique(`code`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;
	

/**
 * Table: Coupon_Models
 */
drop table if exists `coupon_models`;
create table if not exists `coupon_models`(
	`id` bigint unsigned auto_increment not null,
	`category_id` bigint unsigned not null,
	`code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`money_type` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`min_charge_money` decimal(10, 2) unsigned not null,
	`discount_money` decimal(10, 2) unsigned not null,
	`begin_time` bigint unsigned not null,
	`end_time` bigint unsigned not null,
	`status` enum('enabled', 'disabled', 'expired') character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`category_id`) references `coupon_categories`(`id`),
	unique(`code`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;
	
	
/**
 * Table: Coupons
 */
drop table if exists `coupons`;
create table if not exists `coupons`(
	`id` bigint unsigned auto_increment not null,
	`unique_id` char(34) character set utf8mb4 collate utf8mb4_bin not null,
	`category_id` bigint unsigned not null,
	`model_id` bigint unsigned not null,
	`user_id` bigint unsigned not null,
	`order_id` bigint unsigned null,
	`money_type` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`min_charge_money` decimal(10, 2) unsigned not null,
	`discount_money` decimal(10, 2) unsigned not null,
	`begin_time` bigint unsigned not null,
	`end_time` bigint unsigned not null,
	`use_time` bigint unsigned null,
	`status` enum('unused', 'used', 'expired') character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`category_id`) references `coupon_categories`(`id`),
	-- foreign key(`model_id`) references `coupon_models`(`id`),
	-- foreign key(`user_id`) references `users`(`id`),
	-- foreign key(`order_id`) references `orders`(`id`),
	unique(`unique_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;	
	
	
/**
 * Part 8: Message
 */
 
 
/**
 * Table: Message_Default_Params
 */
 drop table if exists `message_default_params`;
 create table if not exists `message_default_params`(
	`id` bigint unsigned auto_increment not null,
	`max_read_keep_duration` int unsigned not null,
	`add_time` bigint unsigned not null,
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Table: Message_Categories
 */
 drop table if exists `message_categories`;
 create table if not exists `message_categories`(
	`id` bigint unsigned auto_increment not null,
	`code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`description` varchar(200) character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	unique(`code`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Table: Message_Templates
 */
drop table if exists `message_templates`;
create table if not exists `message_templates`
(
	`id` bigint unsigned auto_increment not null,
	`category_id` bigint unsigned not null,
	`code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`title` varchar(50) character set utf8mb4 collate utf8mb4_bin not null,
	`description` varchar(500) character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`category_id`) references `message_categories`(`id`),
	unique(`code`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;	


/**
 * Table: Messages
 */
drop table if exists `messages`;
create table if not exists `messages`
(
	`id` bigint unsigned auto_increment not null,
	`unique_id` char(40) character set utf8mb4 collate utf8mb4_bin not null,
	`category_id` bigint unsigned not null,
	`user_id` bigint unsigned not null,
	`title` varchar(50) character set utf8mb4 collate utf8mb4_bin not null,
	`description` varchar(500) character set utf8mb4 collate utf8mb4_bin not null,
	`read_time` bigint unsigned null,
	`status` enum('unread', 'read') character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`category_id`) references `message_categories`(`id`),
	-- foreign key(`user_id`) references `users`(`id`),
	unique(`unique_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;

	
/**
 * Part 9: Express
 */
 
 
/**
 * Table: Express_Default_Params
 */
drop table if exists `express_default_params`;
create table if not exists `express_default_params`(
	`id` bigint unsigned auto_increment not null,
	`max_carriage_template_num` int unsigned not null,
	`max_corporation_num` int unsigned not null,
	`add_time` bigint unsigned not null,
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;		
	
	
/**
 * Table: Express_Address_Regions
 */
drop table if exists `express_address_regions`;
create table if not exists `express_address_regions`(
	`id` bigint unsigned auto_increment not null,
	`code` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`status` enum('enabled', 'disabled') character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	unique(`code`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;	
	
	
/**
 * Table: Express_Address_Provinces
 */
drop table if exists `express_address_provinces`;
create table if not exists `express_address_provinces`(
	`id` bigint unsigned auto_increment not null,
	`region_id` bigint unsigned not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`status` enum('enabled', 'disabled') character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`region_id`) references `express_address_regions`(`id`),
	unique(`region_id`,`name`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;	
	
	
/**
 * Table: Express_Address_Cities
 */
drop table if exists `express_address_cities`;
create table if not exists `express_address_cities`(
	`id` bigint unsigned auto_increment not null,
	`region_id` bigint unsigned not null,
	`province_id` bigint unsigned not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`status` enum('enabled', 'disabled') character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`region_id`) references `express_address_regions`(`id`),
	-- foreign key(`province_id`) references `express_address_provinces`(`id`),
	unique(`province_id`,`name`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;	
	
	
/**
 * Table: Express_Address_Counties
 */
drop table if exists `express_address_counties`;
create table if not exists `express_address_counties`(
	`id` bigint unsigned auto_increment not null,
	`region_id` bigint unsigned not null,
	`province_id` bigint unsigned not null,
	`city_id` bigint unsigned not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`status` enum('enabled', 'disabled') character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`region_id`) references `express_address_regions`(`id`),
	-- foreign key(`province_id`) references `express_address_provinces`(`id`),
	-- foreign key(`city_id`) references `express_address_cities`(`id`),
	unique(`city_id`,`name`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;	
	
	
/**
 * Table: Express_Address_Towns
 */
drop table if exists `express_address_towns`;
create table if not exists `express_address_towns`
(
	`id` bigint unsigned auto_increment not null,
	`region_id` bigint unsigned not null,
	`province_id` bigint unsigned not null,
	`city_id` bigint unsigned not null,
	`county_id` bigint unsigned not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`status` enum('enabled', 'disabled') character set utf8mb4 collate utf8mb4_bin not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`region_id`) references `express_address_regions`(`id`),
	-- foreign key(`province_id`) references `express_address_provinces`(`id`),
	-- foreign key(`city_id`) references `express_address_cities`(`id`),
	-- foreign key(`county_id`) references `express_address_counties`(`id`),
	unique(`county_id`,`name`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Table: Express_Carriage_Templates
 */
drop table if exists `express_carriage_templates`;
create table if not exists `express_carriage_templates`(
	`id` bigint unsigned auto_increment not null,
	`code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`money_type` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`basic_money` decimal(10, 2) unsigned not null,
	`progress_money` decimal(10, 2) unsigned not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	unique(`code`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;	
	
	
/**
 * Table: Express_Carriage_Template_Details
 */
drop table if exists `express_carriage_template_details`;
create table if not exists `express_carriage_template_details`(
	`id` bigint unsigned auto_increment not null,
	`template_id` bigint unsigned not null,
	`region_id` bigint unsigned not null,
	`province_id` bigint unsigned null,
	`money_type` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`basic_money` decimal(10, 2) unsigned not null,
	`progress_money` decimal(10, 2) unsigned not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`template_id`) references `express_carriage_templates`(`id`),
	-- foreign key(`region_id`) references `express_address_regions`(`id`),
	-- foreign key(`province_id`) references `express_address_provinces`(`id`),
	unique(`template_id`, `region_id`, `province_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;	

	
/**
 * Table: Express_Corporations
 */
drop table if exists `express_corporations`;
create table if not exists `express_corporations`(
	`id` bigint unsigned auto_increment not null,
	`code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`name` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`home_page` varchar(100) character set utf8mb4 collate utf8mb4_bin null,
	`is_default` boolean default 0,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	unique(`code`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;
	
	
/**
 * Table: Expresses
 */
drop table if exists `expresses`;
create table if not exists `expresses`
(
	`id` bigint unsigned auto_increment not null,
	`unique_id` char(26) character set utf8mb4 collate utf8mb4_bin not null,
	`corporation_id` bigint unsigned not null,
	`order_id` bigint unsigned not null,
	`code` varchar(30) character set utf8mb4 collate utf8mb4_bin not null,
	`money_type` char(3) character set utf8mb4 collate utf8mb4_bin not null,
	`carriage_money` decimal(10, 2) unsigned not null,
	`address` varchar(200) character set utf8mb4 collate utf8mb4_bin not null,
	`receiver` varchar(40) character set utf8mb4 collate utf8mb4_bin not null,
	`phone` varchar(20) character set utf8mb4 collate utf8mb4_bin not null,
	`postcode` varchar(20) character set utf8mb4 collate utf8mb4_bin null,
	`note` varchar(200) character set utf8mb4 collate utf8mb4_bin null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`corporation_id`) references `express_corporations`(`id`),
	-- foreign key(`order_id`) references `orders`(`id`),
	unique(`unique_id`),
	unique(`corporation_id`, `code`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Part 10: Tax
 */
 
 
/**
 * Table: Tax_Default_Params
 */
drop table if exists `tax_default_params`;
create table if not exists `tax_default_params`
(
	`id` bigint unsigned auto_increment not null,
	`add_time` bigint unsigned not null,
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;


/**
 * Table: Taxes
 */
drop table if exists `taxes`;
create table if not exists `taxes`
(
	`id` bigint unsigned auto_increment not null,
	`express_address_region_id` bigint unsigned not null,
	`product_category_id` bigint unsigned not null,
	`rate` decimal(4, 3) unsigned not null,
	`last_edit_time` bigint unsigned not null,
	`add_time` bigint unsigned not null,
	-- foreign key(`express_address_region_id`) references `express_address_regions`(`id`),
	-- foreign key(`product_category_id`) references `product_categories`(`id`),
	unique(`express_address_region_id`, `product_category_id`),
	primary key(`id`)
)
	engine innodb
	default character set utf8mb4
	default collate utf8mb4_bin;




