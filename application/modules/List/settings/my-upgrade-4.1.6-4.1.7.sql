-- engine4_list_clasfvideos
alter table engine4_list_clasfvideos add index(listing_id);
-- engine4_list_itemofthedays
alter table engine4_list_itemofthedays add index(listing_id);
alter table engine4_list_itemofthedays add index(date);
alter table engine4_list_itemofthedays add index(endtime);
-- engine4_list_ratings
alter table engine4_list_ratings add index(listing_id);
alter table engine4_list_ratings add index(user_id);
-- engine4_list_reviews
alter table engine4_list_reviews add index(listing_id);
alter table engine4_list_reviews add index(owner_id);
-- engine4_list_vieweds
alter table engine4_list_vieweds add index(listing_id);
alter table engine4_list_vieweds add index(viewer_id);

