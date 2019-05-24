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
insert into `managers`(`id`, `role_id`, `name`, `phone`, `pwd`, `last_edit_time`, `add_time`) values
		(2, 1, 'Goodman.Saul', '18630856246', SHA2('goodwin$000', 512), UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
 
 
/**
 * Part 3: User
 */
insert into `user_carts`(`id`, `unique_id`, `user_id`, `product_id`, `product_model_id`, `product_unique_id`, `product_code`, `product_name`, `product_model_description`, `quantity`, `status`, `last_edit_time`, `add_time`) values
		(1, '7ID9UIHPVR5NPRKG60IKBJJ1QSMG0K', 1, 5, 6, 'M07HZ-9JGOPN-BOG-HONT1', 'iPhone-XS', 'iPhone XS', 'Color**Space Gray,,Storage**512GB', 2, 'online', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 'W8QWRVTT9MYAERMH0HNRN2KACKNEER', 1, 6, 9, 'NFAMO-Z3RYRD-3DM-BAXDT', 'iPhone-XS-Max', 'iPhone XS Max', 'Color**Space Gray,,Storage**512GB', 1, 'online', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `user_collections`(`id`, `unique_id`, `user_id`, `product_id`, `product_unique_id`, `product_code`, `product_name`, `status`, `last_edit_time`, `add_time`) values
		(1, 'ZWPGV54VC2UXAG5ITNU11EQYTFBAAZ', 1, 5, 'M07HZ-9JGOPN-BOG-HONT1', 'iPhone-XS', 'iPhone XS', 'online', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 'XRASU7HULRDLCWMJDSYYWVJUOQWLHY', 1, 6, 'NFAMO-Z3RYRD-3DM-BAXDT', 'iPhone-XS-Max', 'iPhone XS Max', 'online', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `user_footmarks`(`id`, `unique_id`, `user_id`, `product_id`, `product_unique_id`, `product_code`, `product_name`, `status`, `last_edit_time`, `add_time`) values
		(1, 'EEI5UXX3JQW2HBSVACASJZWP06G6NL', 1, 5, 'M07HZ-9JGOPN-BOG-HONT1', 'iPhone-XS', 'iPhone XS', 'online', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 'VW4GSCFXXB3M1JGBKF1WLOM1VQ0WLN', 1, 6, 'NFAMO-Z3RYRD-3DM-BAXDT', 'iPhone-XS-Max', 'iPhone XS Max', 'online', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `user_delivery_addresses`(`id`, `unique_id`, `user_id`, `express_address_region_id`, `express_address_province_id`, `express_address_city_id`, `express_address_county_id`, `express_address_town_id`, `primary_address`, `detail_address`, `receiver`, `phone`, `postcode`, `is_default`, `status`, `last_edit_time`, `add_time`) values
		(1, 'SWNVPHLP0GFS3HGE9SVVMF59JICU4W', 1, 1, 3, 48, 722, NULL, 'China, Tianjin, Jinnan, HaiHe Eduction Park', 'No.1 Yaguan Road', 'White.Walter', '18630856246', '300350', 1, 'enabled', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 'A7QPQ5HL4QCVMZPKXPVZ87FK3OAUGH', 1, 1, 3, 48, 718, NULL, 'China, Tianjin, Jinnan, ShuangGang', 'CuiGangYuan B2-1-1002', 'White.Walter', '18639856246', '300350', 0, 'enabled', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `user_reviews`(`id`, `unique_id`, `user_id`, `order_id`, `product_id`, `product_model_id`, `product_unique_id`, `product_code`, `product_name`, `product_model_description`, `grade`, `description`, `last_edit_time`, `add_time`) values
		(1, 'VKQQ0SYXBIPGM8SN9C9BERVFJU2BER', 1, 1, 5, 5, 'M07HZ-9JGOPN-BOG-HONT1', 'iPhone-XS', 'iPhone XS', 'Color**Sliver,,Storage**512GB', 5, 'Mobile phone is very easy to use. Opening app is very smooth.' , UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 'HWTMBCABS9QFO8GF1XA7RPJZJI0GAT', 1, 1, 5, 6, 'M07HZ-9JGOPN-BOG-HONT1', 'iPhone-XS', 'iPhone XS', 'Color**Space Gray,,Storage**512GB', 5, 'The appearance is gorgeous and the screen size is large enough.', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `users`(`id`, `unique_id`, `open_id`, `nickname`, `sex`, `head_img_url`, `real_name`, `phone`, `pwd`, `balance`, `growth`, `point`, `status`, `last_edit_time`, `add_time`) values
		(1, 'P0FYA-OINLQ04B-OT9ASUG-C1FLM', 'kIaWQ2MjDNWWEODd3LWe0m3CRpQntBfQvT3m8r0CIZWIIjrPM7n4lkAgDrrwUCvwvHpMfE25LdjvHLsPVUQLlajmJkaqMKJZVWhOmM6kAmXmBAMdUCh2LxuwleFOnhHl', 'White.Walter', 'male', NULL, NULL, NULL, NULL, 0.00, 0, 0, 'unregistered', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 
		(2, 'HNDEE-PQ1KEKGI-DSMNZT2-V3B4K', 'dMvPI4S9chvPlK4DHECvZWiO6Qt4ZLfGpVHAQI29AvyodNmoJW0Qfo4Ut0f4NELapA1VxdScji5GA4p2TNDJaHnMQmL4NhZSqUUwsek393CLMqSZxAcjezocyOjuLxI5', 'Pinkman.Jesse', 'male', NULL, NULL, NULL, NULL, 0.00, 0, 0, 'unregistered', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());


/**
 * Part 4: Product
 */
insert into `product_categories`(`id`, `code`, `name`, `place`, `last_edit_time`, `add_time`) values
		(1, 'Mac', 'Mac', 1000010, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 'iPad', 'iPad', 1000020, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 'iPhone', 'iPhone', 1000030, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, 'Watch', 'Watch', 1000040, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `product_category_properties`(`id`, `category_id`, `name`, `last_edit_time`, `add_time`) values
		(1, 1, 'Color', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 1, 'Processor', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 1, 'Memory', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, 1, 'Storage', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(5, 2, 'Color', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(6, 2, 'Storage', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(7, 2, 'Connectivity', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(8, 3, 'Color', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(9, 3, 'Storage', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(10, 4, 'Color', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(11, 4, 'Size', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 
		(12, 4, 'Connectivity', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `product_groups` (`id`, `code`, `name`, `place`, `last_edit_time`, `add_time`) values
		(1, 'Mac', 'Mac', 2000010, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 'iPad', 'iPad', 2000020, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 'iPhone', 'iPhone', 2000030, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, 'Watch', 'Watch', 2000040, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `product_group_details`(`id`, `group_id`, `product_id`, `last_edit_time`, `add_time`) values
		(1, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 1, 2, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),		
		(3, 2, 3, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, 2, 4, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(5, 3, 5, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(6, 3, 6, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(7, 3, 7, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(8, 3, 8, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(9, 4, 9, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `product_subgroups` (`id`, `group_id`, `code`, `name`, `place`, `last_edit_time`, `add_time`) values
		(1, 1, 'MacBook', 'MacBook', 3000010, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 1, 'MacBook-Air', 'MacBook Air', 3000020, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 1, 'MacBook-Pro', 'MacBook Pro', 3000030, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, 1, 'iMac', 'iMac', 3000040, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(5, 1, 'iMac-Pro', 'iMac Pro', 3000050, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(6, 1, 'Mac-Pro', 'Mac Pro', 300060, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(7, 1, 'Mac-Mini', 'Mac Mini', 3000070, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),	
		(8, 2, 'iPad-Pro', 'iPad Pro', 3000080, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),		
		(9, 2, 'iPad-Air', 'iPad Air', 3000090, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(10, 2, 'iPad', 'iPad', 3000100, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(11, 2, 'iPad-Mini', 'iPad-Mini', 3000110, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(12, 3, 'iPhone-XS', 'iPhone XS', 3000120, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(13, 3, 'iPhone-XR', 'iPhone XR', 3000130, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(14, 3, 'iPhone-8', 'iPhone 8', 3000140, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(15, 3, 'iPhone-7', 'iPhone 7', 3000150, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(16, 4, 'Apple-Watch-Series-4', 'Apple Watch Serices 4', 3000160, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(17, 4, 'Apple-Watch-Nike+', 'Apple Watch Nike+', 3000170, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(18, 4, 'Apple-Watch-Series-3', 'Apple Watch Serices 3', 3000180, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `product_subgroup_details`(`id`, `group_id`, `subgroup_id`, `product_id`, `last_edit_time`, `add_time`) values
		(1, 1, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 1, 4, 2, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 2, 10, 3, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, 2, 11, 4, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(5, 3, 12, 5, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 
		(6, 3, 12, 6, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(7, 3, 14, 7, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(8, 3, 14, 8, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 
		(9, 4, 16, 9, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `product_videos`(`id`, `product_id`, `file_name`, `place`, `last_edit_time`, `add_time`) values
		(1, 5, 'v4MEGXpVfZBzuZoKhDx0HCrRUGjRwJZBwj1N2TGy.mp4', 5000010, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 6, 'Two09Euu0zhMENH3ozoSpl1kAefCwbIrzkn6WJqH.mp4', 5000020, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `product_pictures`(`id`, `product_id`, `file_name`, `place`, `last_edit_time`, `add_time`) values
		(1, 1, '2iXlrSGfqiITP4sSyIa5Q9GIJbUSnJPKWS9DSuAB.png', 6000010, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 2, 'iDV18fOsAt3tepGo8XIfRA1n4t6HGSKTaLuqTgAa.png', 6000020, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 3, '4ETpfvSR5l1SY0fVRVUUAgp1Lc5dF5B8TtnJI6tx.png', 6000030, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),	
		(4, 4, 'fgslx6Wvd5OXouLpKuVuAlyUhrQW7VeaMq421EXe.png', 6000040, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),		
		(5, 5, 'O3NABTsmsQs9OjMMfB0lxSKDgkkO5uMUyP2h6rPM.png', 6000050, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),			
		(6, 5, 'zFccFGK1fj8QY1rpWozKzF5e5baoixt5pgyo6eUJ.png', 6000060, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),	
		(7, 5, 'wcZCgtEOcuz9GSqoH4PWRKFyHyD0mgpADF1ACXLW.png', 6000070, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),	
		(8, 5, 'Ak4mZ7DqfW5EutZmXPpOhEm9YjFMIcc21ihaxGKY.png', 6000080, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),			
		(9, 6, '8ZYMj9uT7U7F3Nqflkdh7JXjuXuboRCWmez4WoxU.png', 6000090, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),						
		(10, 7, 'qU4LiwLtolbWnTAinCvzj6lH1nSIgRsyNPoe3AvL.png', 6000100, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),			
		(11, 8, 'AXLaeLqjvcNZ610rgaYAdlzgTmEy2Xxtuu3EdBNG.png', 6000110, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),	
		(12, 9, 'a8iG60TATqtbdK98j3kSVHGYEY5eTHq6o7G7Q4l0.png', 6000120, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `product_description_pictures`(`id`, `product_id`, `file_name`, `place`, `last_edit_time`, `add_time`) values
		(1, 1, 'FyCEAPiXV4SrcOien5U6ki871FqNuiziM8GbUW2a.png', 7000010, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 2, 'MscOuu1nkIuDbkqQzmz2Xr5dlRYEk7dJMfdMwsRd.png', 7000020, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 3, '0NeQIOtRfZTRa1UwSSwcvMPTsEasnHIE6tgyBWIc.png', 7000030, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),		
		(4, 4, 'WFt5F1fAhJGZ36w8B8BWpLSBUt3m80YlgbDN6Mrp.png', 7000040, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(5, 5, 'NWDs3CB8N5m6C22KVhjU2AyNaHdFk7AHUbxTqlgw.png', 7000050, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(6, 5, 'w9ZLrtP0Z0fCZYwA4c9zOIysgzCHteiYkx0JzASZ.png', 7000060, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(7, 5, 'pmZ02msyVeoAVhiOgpU2vUBkG25uk1Fnrzud9TD4.png', 7000070, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(8, 5, 'nHDwO9eEYKJOxAfxcJbVsHuNrPBQCOP1jtgAhksN.png', 7000080, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(9, 5, 'cqnSnAKOQhYRveiHmFE6yCKK8L8TGoxshF96w99K.png', 7000090, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(10, 5, 'NID8P9RupGM7uY3CuOROGRTRbhamXhd47VWthxkk.png', 7000100, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(11, 5, '9eN3xcTp1ZBbTP6fEBllCtO4vbbkLxJCWCAQ9YYN.png', 7000110, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(12, 5, 'vQMwUYFJRqwTg5xaIPoDknBDP8iNZEm95Ja3VDfX.png', 7000120, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(13, 5, 'cuudR4PJJx4rJeAgoZrbkfPbEeFtTXYXq4hcMETg.png', 7000130, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(14, 5, 'oCg1trTc2bQ8teUDNFZTtvl9QkvEzXH0NI1ygGjx.png', 7000140, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(15, 6, '4L8wusJRRnW3IxCQeiYsGbXjpUS5Dum5NVbHMLdZ.png', 7000150, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(16, 7, '82OFVRglMXxElmRuusJec7ZJ8K98KsuILKc0ZzNM.png', 7000160, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(17, 8, 'xdomZIITLW5PASYEGVx6c3M6bDtPunZeiEJbEKjc.png', 7000170, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(18, 9, 'tKmu7bczvFKUu04f2xISWl0RKGenQVnNosWex9fj.png', 7000180, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `product_property_enums`(`id`, `product_id`, `property_id`, `data`, `last_edit_time`, `add_time`) values
		(1, 1, 1, 'Space Gray', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 1, 2, 'Core i7 1.4GHz-Dual-Core', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 1, 3, 'DDR3 16GB', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, 1, 4, 'SSD 512GB', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(5, 2, 1, 'Space Gray', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(6, 2, 2, 'Core i5 3.7GHz-6-Core', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(7, 2, 3, 'DDR4 8GB', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(8, 2, 4, 'Fusion 2TB', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(9, 3, 5, 'Space Gray', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(10, 3, 6, '128GB', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(11, 3, 7, 'Wi-Fi+Cellular', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(12, 4, 5, 'Space Gray', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(13, 4, 6, '256GB', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(14, 4, 7, 'Wi-Fi+Cellular', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(15, 5, 8, 'Silver', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(16, 5, 8, 'Space Gray', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(17, 5, 8, 'Golden', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(18, 5, 9, '512GB', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(19, 6, 8, 'Silver', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(20, 6, 8, 'Space Gray', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(21, 6, 8, 'Golden', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(22, 6, 9, '512GB', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(23, 7, 8, 'Space Gray', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(24, 7, 9, '256GB', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(25, 8, 8, 'Space Gray', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(26, 8, 9, '256GB', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(27, 9, 10, 'Space Gray+Black Sport Band', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(28, 9, 11, '44mm', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(29, 9, 12, 'GPS+Cellular', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `product_models`(`id`, `product_id`, `description`, `money_type`, `tag_price`, `discount_price`, `stock_num`, `last_edit_time`, `add_time`) values
		(1, 1, 'Color**Space Gray,,Processor**Core i7 1.4GHz-dual-Core,,Memory**DDR3 16GB,,Storage**SSD 512GB', 'CNY', 14433.00, 14433.00, 82, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 
		(2, 2, 'Color**Space Gray,,Processor**Core i5 3.7GHz-6-Core,,Memory**DDR4 8GB,,Storage**Fusion 2TB', 'CNY', 17728.00, 17728.00, 118, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 
		(3, 3, 'Color**Space Gray,,Storage**128GB,,Connectivity**Wi-Fi+Cellular', 'CNY', 4238.00, 4238.00, 24, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 
		(4, 4, 'Color**Space Gray,,Storage**256GB,,Connectivity**Wi-Fi+Cellular', 'CNY', 5065.00, 5065.00, 169, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 
		(5, 5, 'Color**Sliver,,Storage**512GB', 'CNY', 11399.00, 11399.00, 425, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 
		(6, 5, 'Color**Space Gray,,Storage**512GB', 'CNY', 11399.00, 11399.00, 322, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 
		(7, 5, 'Color**Golden,,Storage**512GB', 'CNY', 11399.00, 11399.00, 400, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 
		(8, 6, 'Color**Sliver,,Storage**512GB', 'CNY', 12299.00, 12299.00, 293, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 
		(9, 6, 'Color**Space Gray,,Storage**512GB', 'CNY', 12299.00, 12299.00, 115, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 		
		(10, 6, 'Color**Golden,,Storage**512GB', 'CNY', 12299.00, 12299.00, 506, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 		
		(11, 7, 'Color**Space Gray,,Storage**256GB', 'CNY', 6199.00, 6199.00, 188, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 
		(12, 8, 'Color**Space Gray,,Storage**256GB', 'CNY', 7099.00, 7099.00, 201, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 
		(13, 9, 'Color**Space Gray+Black Sport Band,,Size**44mm,,Connectivity**GPS+Cellular', 'CNY', 4188.00, 4188.00, 57, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `products`(`id`, `unique_id`, `category_id`, `express_carriage_template_id`, `primary_video`, `primary_picture`, `code`, `name`, `money_type`, `min_tag_price`, `min_discount_price`, `place`, `click_num`, `sale_num`, `status`, `last_edit_time`, `add_time`) values
		(1, 'UIQJN-JNGOFL-7IL-ANNKZ', 1, 1, NULL, '2iXlrSGfqiITP4sSyIa5Q9GIJbUSnJPKWS9DSuAB.png', 'MacBook', 'MacBook', 'CNY', 14433.00, 14433.00, 4000010, 0, 120, 'online', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 'RZPQ5-X3KNTM-VDJ-BCDYR', 1, 1, NULL, 'iDV18fOsAt3tepGo8XIfRA1n4t6HGSKTaLuqTgAa.png', 'iMac', 'iMac', 'CNY', 17728.00, 17728.00, 4000020, 0, 468, 'online', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 'NPXRD-D4BPZW-NZX-RM5U3', 2, 1, NULL, '4ETpfvSR5l1SY0fVRVUUAgp1Lc5dF5B8TtnJI6tx.png', 'iPad', 'iPad', 'CNY', 4238.00, 4238.00, 4000030, 0, 29866, 'online', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, 'MSQRX-DCLKWQ-UJ8-WRRYN', 2, 1, NULL, 'fgslx6Wvd5OXouLpKuVuAlyUhrQW7VeaMq421EXe.png', 'iPad-Mini', 'iPad Mini', 'CNY', 5065.00, 5065.00, 4000040, 0, 28922, 'online', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(5, 'M07HZ-9JGOPN-BOG-HONT1', 3, 1, 'v4MEGXpVfZBzuZoKhDx0HCrRUGjRwJZBwj1N2TGy.mp4', 'O3NABTsmsQs9OjMMfB0lxSKDgkkO5uMUyP2h6rPM.png', 'iPhone-XS', 'iPhone XS', 'CNY', 11399.00, 11399.00, 4000050, 0, 18759, 'online', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(6, 'NFAMO-Z3RYRD-3DM-BAXDT', 3, 1, 'Two09Euu0zhMENH3ozoSpl1kAefCwbIrzkn6WJqH.mp4', '8ZYMj9uT7U7F3Nqflkdh7JXjuXuboRCWmez4WoxU.png', 'iPhone-XS-Max', 'iPhone XS Max', 'CNY', 12299.00, 12299.00, 4000060, 0, 21493, 'online', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),		
		(7, 'E9EOE-PYIUGF-ROX-Z7RHO', 3, 1, NULL, 'qU4LiwLtolbWnTAinCvzj6lH1nSIgRsyNPoe3AvL.png', 'iPhone-8', 'iPhone 8', 'CNY', 6199.00, 6199.00, 4000070, 0, 20333, 'online', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),		
		(8, 'AOF45-RZ70XO-YUK-GGDLX', 3, 1, NULL, 'AXLaeLqjvcNZ610rgaYAdlzgTmEy2Xxtuu3EdBNG.png', 'iPhone-8-Plus', 'iPhone 8 Plus', 'CNY', 7099.00, 7099.00, 4000080, 0, 38283, 'online', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(9, 'NRXPR-LNM1AU-IXC-YDHMY', 4, NULL, NULL, 'a8iG60TATqtbdK98j3kSVHGYEY5eTHq6o7G7Q4l0.png', 'Apple-Watch-Series-4', 'Apple Watch Serices 4', 'CNY', 4188.00, 4188.00, 4000090, 0, 10025, 'offline', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

		
/**
 * Part 5: Order
 */   
insert into `order_details`(`id`, `order_id`, `product_id`, `product_model_id`, `product_unique_id`, `product_code`, `product_name`, `product_model_description`, `money_type`, `tag_price`, `discount_price`, `quantity`, `last_edit_time`, `add_time`) values
		(1, 1, 5, 5, 'M07HZ-9JGOPN-BOG-HONT1', 'iPhone-XS', 'iPhone XS', 'Color**Sliver,,Storage**512GB', 'CNY', 11399.00, 11399.00, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 1, 5, 6, 'M07HZ-9JGOPN-BOG-HONT1', 'iPhone-XS', 'iPhone XS', 'Color**Space Gray,,Storage**512GB', 'CNY', 11399.00, 11399.00, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `orders`(`id`, `unique_id`, `user_id`, `coupon_id`, `money_type`, `total_product_tag_money`, `total_product_discount_money`, `total_tax_money`, `total_express_carriage_money`, `total_coupon_discount_money`, `pay_money`, `address`, `receiver`, `phone`, `postcode`, `note`, `pay_method`, `pay_time`, `ship_time`, `complete_time`, `review_time`, `cancel_reason`, `cancel_time`, `close_reason`, `close_time`, `close_manager`, `status`, `last_edit_time`, `add_time`) values
		(1, 'XSMK1-XPUVWB-MVL-JOHQM', 1, NULL, 'CNY', 22798.00, 22798.00, 0.00, 50.00, 0.00, 22848.00, 'China, TianJin, JinNan, ShuangGang, JinDiGeLinShiJie, PaoPaoTui', 'ProfoundBlue', '18630856246', '300350', NULL, 'weixin', UNIX_TIMESTAMP()+1000, UNIX_TIMESTAMP()+2000, UNIX_TIMESTAMP()+3000, UNIX_TIMESTAMP()+4000, NULL, NULL, NULL, NULL, NULL, 'reviewed', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
		
		
/**
 * Part 6: Gift
 */
insert into `gifts`(`id`, `unique_id`, `category_id`, `model_id`, `user_id`, `code`, `money_type`, `recharge_money`, `pay_money`, `pay_method`, `pay_time`, `recharge_time`, `status`, `last_edit_time`, `add_time`) values
		(1, 'CFD0Q3VF-ABJ4KI-GWGGK-YPKU5SKL', 1, 1, 2, 'CFD0Q3VF-ABJ4KI-GWGGK-YPKU5SKL', 'CNY', 100.00, 100.00, 'weixin', UNIX_TIMESTAMP()+3600, UNIX_TIMESTAMP()+3600*2, 'recharged', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 'TWUJJDYZ-GQKYVW-EDPB5-OVYVSHTT', 1, 1, 2, 'TWUJJDYZ-GQKYVW-EDPB5-OVYVSHTT', 'CNY', 100.00, 100.00, 'weixin', UNIX_TIMESTAMP()+3600, NULL, 'paid', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 'ZXETRKS5-JXXMGZ-FEP0R-OJEKRCFJ', 1, 1, 2, 'ZXETRKS5-JXXMGZ-FEP0R-OJEKRCFJ', 'CNY', 100.00, 100.00, NULL, NULL, NULL, 'unpaid', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, 'XOJDIGKW-SVHXIM-UTRKS-ZQ3ENFQL', 1, 1, 2, 'XOJDIGKW-SVHXIM-UTRKS-ZQ3ENFQL', 'CNY', 100.00, 100.00, NULL, NULL, NULL, 'unpaid', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(5, 'ECTRZ5UY-5PFTJK-VMZW8-YLFRUZRA', 1, 1, 2, 'ECTRZ5UY-5PFTJK-VMZW8-YLFRUZRA', 'CNY', 100.00, 100.00, NULL, NULL, NULL, 'unpaid', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
 
 
/**
 * Part 7: Coupon
 */
insert into `coupon_models`(`id`, `category_id`, `code`, `name`, `money_type`, `min_charge_money`, `discount_money`, `begin_time`, `end_time`, `status`, `last_edit_time`, `add_time`) values
		(1, 1, '5-Red-Packet', '5-Red.Packet', 'CNY',  0.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()+3600*24*7, 'enabled', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 
		(2, 2, '10-Full-Range-Coupon', '10-Full.Range.Coupon', 'CNY', 100.00, 10.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()+3600*24*7, 'enabled', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 
		(3, 2, '20-Full-Range-Coupon', '20-Full.Range.Coupon', 'CNY', 200.00, 20.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()+3600*24*7, 'enabled', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, 2, '50-Full-Range-Coupon', '50-Full.Range.Coupon', 'CNY', 500.00, 50.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()+3600*24*7, 'enabled', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `coupons`(`id`, `unique_id`, `category_id`, `model_id`, `user_id`, `order_id`, `money_type`, `min_charge_money`, `discount_money`, `begin_time`, `end_time`, `use_time`, `status`, `last_edit_time`, `add_time`) values
		(1, 'DIEGLKJF-N2QFSNAWL-FDPILT-QVYBUMKH', 1, 1, 1, NULL, 'CNY', 0.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()+3600*24*7, NULL, 'unused', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()), 
		(2, 'BJDH4N8A-XGOAQV6JT-C4FNCC-WNTMW2AN', 2, 2, 1, NULL, 'CNY', 100.00, 10.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()+3600*24*7, NULL, 'unused', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 'PJTGDSZI-QKEKJSNE6-Z5REBM-FWRAXNKS', 2, 2, 1, NULL, 'CNY', 100.00, 10.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()+3600*24*7, NULL, 'unused', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, 'U9GB5CLM-AAUIBYIG5-HUHCRT-D9ZCBQGI', 2, 3, 1, NULL, 'CNY', 200.00, 20.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()+3600*24*7, NULL, 'unused', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(5, 'VGMCSYG9-MU89HYVSP-YNZBEU-O6BHUPQV', 2, 3, 1, NULL, 'CNY', 200.00, 20.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()+3600*24*7, NULL, 'unused', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
 
 
/**
 * Part 8: Message
 */
insert into `Messages`(`id`, `unique_id`, `category_id`, `user_id`, `title`, `description`, `read_time`, `status`, `last_edit_time`, `add_time`) values
		(1, 'OVPLCR-S7DB6FAC-EDBHD0-NYQG3ENY-31BCVMJQ', 6, 1, 'Say Hello', 'Just say hello to you, -)-', UNIX_TIMESTAMP()+10, 'read', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 'LRYBCA-HNEBOXG3-NXNC9P-1IDTSPR3-FJY2EABJ', 6, 1, 'Say Hello', 'Just say hello to you again, -)-', NULL, 'unread', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
 
 
/**
 * Part 9: Express
 */
insert into `express_address_regions`(`id`, `code`, `name`, `status`, `last_edit_time`, `add_time`) values
		(2, 'USA', 'United States', 'enabled', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `express_carriage_templates`(`id`, `code`, `name`, `money_type`, `basic_money`, `progress_money`, `last_edit_time`, `add_time`) values
		(2, 'Basic-Carriage-Template-No.2', 'Basic.Carriage.Template-No.2', 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
insert into `express_carriage_template_details`(`id`, `template_id`, `region_id`, `province_id`, `money_type`, `basic_money`, `progress_money`, `last_edit_time`, `add_time`) values
		(34, 1, 2, NULL, 'CNY', 8.00, 4.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(35, 2, 1, 1, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(36, 2, 1, 2, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(37, 2, 1, 3, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(38, 2, 1, 4, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(39, 2, 1, 5, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(40, 2, 1, 6, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(41, 2, 1, 7, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(42, 2, 1, 8, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(43, 2, 1, 9, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(44, 2, 1, 10, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(45, 2, 1, 11, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(46, 2, 1, 12, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(47, 2, 1, 13, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(48, 2, 1, 14, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(49, 2, 1, 15, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(50, 2, 1, 16, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(51, 2, 1, 17, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(52, 2, 1, 18, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(53, 2, 1, 19, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(54, 2, 1, 20, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(55, 2, 1, 21, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(56, 2, 1, 22, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(57, 2, 1, 23, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(58, 2, 1, 24, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(59, 2, 1, 25, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(60, 2, 1, 26, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(61, 2, 1, 27, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(62, 2, 1, 28, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(63, 2, 1, 29, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(64, 2, 1, 30, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(65, 2, 1, 31, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(66, 2, 1, 32, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(67, 2, 1, 33, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(68, 2, 2, NULL, 'CNY', 10.00, 5.00, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());	
insert into `expresses`(`id`, `unique_id`, `corporation_id`, `order_id`, `code`, `money_type`, `carriage_money`, `address`, `receiver`, `phone`, `postcode`, `note`, `last_edit_time`, `add_time`) values
		(1, 'SCKEB-6A7IF8Q6E-MK9Y-GZMXM', 2, 1, '3395365225935', 'CNY', 10.00, 'No.1 Yaguan Road, Haihe Education Park, Jinnan District, Tianjin, China', 'White.Walter', '18630856246', '300350', NULL, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 'MTT72-HDWIPFQPG-QCNO-OLRMZ', 2, 1, '3395365225948', 'CNY', 10.00, 'No.1 Yaguan Road, Haihe Education Park, Jinnan District, Tianjin, China', 'White.Walter', '18630856246', '300350', 'Send a gift', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
	

/**
 * Part 10: Tax
 */
 insert into `taxes`(`id`, `express_address_region_id`, `product_category_id`, `rate`, `last_edit_time`, `add_time`) values
		(1, 1, 1, 0.080, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(2, 1, 2, 0.120, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(3, 1, 3, 0.100, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(4, 1, 4, 0.060, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(5, 2, 1, 0.120, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(6, 2, 2, 0.000, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(7, 2, 3, 0.100, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
		(8, 2, 4, 0.150, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

