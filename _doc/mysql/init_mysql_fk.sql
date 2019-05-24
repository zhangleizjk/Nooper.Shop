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
alter table `manager_role_permissions`
		add foreign key(`role_id`) references `manager_roles`(`id`),
		add foreign key(`permission_id`) references `manager_permissions`(`id`);
alter table `managers`
		add foreign key(`role_id`) references `manager_roles`(`id`);
 
 
/**
 * Part 3: User
 */
alter table `user_carts`
		add foreign key(`user_id`) references `users`(`id`),
		add foreign key(`product_id`) references `products`(`id`),
		add foreign key(`product_model_id`) references `product_models`(`id`);
alter table `user_collections`
		add foreign key(`user_id`) references `users`(`id`),
		add foreign key(`product_id`) references `products`(`id`);
alter table `user_footmarks`
		add foreign key(`user_id`) references `users`(`id`),
		add foreign key(`product_id`) references `products`(`id`);
alter table `user_delivery_addresses`
	add foreign key(`user_id`) references `users`(`id`),
	add foreign key(`express_address_region_id`) references `express_address_regions`(`id`),
	add foreign key(`express_address_province_id`) references `express_address_provinces`(`id`),
	add foreign key(`express_address_city_id`) references `express_address_cities`(`id`),
	add foreign key(`express_address_county_id`) references `express_address_counties`(`id`),
	add foreign key(`express_address_town_id`) references `express_address_towns`(`id`);	
alter table `user_reviews`
	add foreign key(`user_id`) references `users`(`id`),
	add foreign key(`order_id`) references `orders`(`id`),
	add foreign key(`product_id`) references `products`(`id`) on delete set null,
	add foreign key(`product_model_id`) references `product_models`(`id`) on delete set null;
alter table `user_balance_records`
		add foreign key(`user_id`) references `users`(`id`);

 
/**
 * Part 4: Product
 */
alter table `product_category_properties`
		add foreign key(`category_id`) references `product_categories`(`id`);
alter table `product_group_details`
		add foreign key(`group_id`) references `product_groups`(`id`),
		add foreign key(`product_id`) references `products`(`id`);
alter table `product_subgroups`
		add foreign key(`group_id`) references `product_groups`(`id`);
alter table `product_subgroup_details`
		add foreign key(`group_id`) references `product_groups`(`id`),
		add foreign key(`subgroup_id`) references `product_subgroups`(`id`),
		add foreign key(`product_id`) references `products`(`id`);
alter table `product_videos`
		add foreign key(`product_id`) references `products`(`id`);
alter table `product_pictures`
		add foreign key(`product_id`) references `products`(`id`);
alter table `product_description_pictures`
		add foreign key(`product_id`) references `products`(`id`);
alter table `product_property_enums`
		add foreign key(`product_id`) references `products`(`id`),
		add foreign key(`property_id`) references `product_category_properties`(`id`);
alter table `product_models`
		add foreign key(`product_id`) references `products`(`id`);
alter table `products`
		add foreign key(`category_id`) references `product_categories`(`id`),
		add foreign key(`express_carriage_template_id`) references `express_carriage_templates`(`id`) on delete set null;


/**
 * Part 5: Order
 */
alter table `order_details`
		add foreign key(`order_id`) references `orders`(`id`),
		add foreign key(`product_id`) references `products`(`id`) on delete set null, 
		add foreign key(`product_model_id`) references `product_models`(`id`) on delete set null; 
alter table `orders`
		add foreign key(`user_id`) references `users`(`id`),
		add foreign key(`coupon_id`) references `coupons`(`id`);
alter table `order_balance_pay_records`
		add foreign key(`order_id`) references `orders`(`id`);
alter table `order_balance_refund_records`
		add foreign key(`order_id`) references `orders`(`id`);


/**
 * Part 6: Gift
 */
alter table `gift_models`
		add foreign key(`category_id`) references `gift_categories`(`id`);
alter table `gifts`
		add foreign key(`category_id`) references `gift_categories`(`id`),
		add foreign key(`model_id`) references `gift_models`(`id`),
		add foreign key(`user_id`) references `users`(`id`);
 
 
/**
 * Part 7: Coupon
 */
alter table `coupon_models`
		add foreign key(`category_id`) references `coupon_categories`(`id`);
alter table `coupons`
		add foreign key(`category_id`) references `coupon_categories`(`id`),
		add foreign key(`model_id`) references `coupon_models`(`id`),
		add foreign key(`user_id`) references `users`(`id`),
		add foreign key(`order_id`) references `orders`(`id`);
 

/**
 * Part 8: Message
 */
alter table `message_templates`
		add  foreign key(`category_id`) references `message_categories`(`id`);
alter table `messages`
		add  foreign key(`category_id`) references `message_categories`(`id`),
		add foreign key(`user_id`) references `users`(`id`);
 
 
/**
 * Part 9: Express
 */
alter table `express_address_provinces`
		add foreign key(`region_id`) references `express_address_regions`(`id`);
alter table `express_address_cities`
		add foreign key(`region_id`) references `express_address_regions`(`id`),
		add foreign key(`province_id`) references `express_address_provinces`(`id`);
alter table `express_address_counties`
		add foreign key(`region_id`) references `express_address_regions`(`id`),
		add foreign key(`province_id`) references `express_address_provinces`(`id`),
		add foreign key(`city_id`) references `express_address_cities`(`id`);
alter table `express_address_towns`
		add foreign key(`region_id`) references `express_address_regions`(`id`),
		add foreign key(`province_id`) references `express_address_provinces`(`id`),
		add foreign key(`city_id`) references `express_address_cities`(`id`),
		add foreign key(`county_id`) references `express_address_counties`(`id`);
alter table `express_carriage_template_details`
		add foreign key(`template_id`) references `express_carriage_templates`(`id`),
		add  foreign key(`region_id`) references `express_address_regions`(`id`),
		add foreign key(`province_id`) references `express_address_provinces`(`id`);
alter table `expresses`
		add foreign key(`corporation_id`) references `express_corporations`(`id`),
		add foreign key(`order_id`) references `orders`(`id`);
 
 
/**
 * Part 10: Tax
 */
alter table `taxes` 
		add foreign key(`express_address_region_id`) references `express_address_regions`(`id`),
		add foreign key(`product_category_id`) references `product_categories`(`id`);
 

 
 
 
 
 