CREATE TABLE  `jos_algolia_item` (
  `id` INTEGER NOT NULL,
  `index_id` INTEGER NOT NULL,
  `object_id` TEXT NOT NULL,
  `name` TEXT DEFAULT NULL,
  `data` TEXT DEFAULT NULL,
  `state` INTEGER NOT NULL,
  `params` TEXT DEFAULT NULL,
  `created_by` INTEGER DEFAULT NULL,
  `created_date` TEXT DEFAULT NULL,
  `modified_by` INTEGER DEFAULT NULL,
  `modified_date` TEXT DEFAULT NULL,
  `checked_out` INTEGER DEFAULT NULL,
  `checked_out_time` TEXT DEFAULT NULL
);
