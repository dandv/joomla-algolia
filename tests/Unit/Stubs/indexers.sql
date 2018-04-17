CREATE TABLE  `jos_algolia_indexers` (
  `id` INTEGER NOT NULL,
  `name` TEXT DEFAULT NULL,
  `extension_id` INTEGER NOT NULL,
  `application_id` TEXT DEFAULT NULL,
  `api_key` TEXT DEFAULT NULL,
  `search_key` TEXT DEFAULT NULL,
  `index_name` TEXT DEFAULT NULL,
  `asset_id` INTEGER NOT NULL,
  `state` INTEGER NOT NULL,
  `params` TEXT DEFAULT NULL,
  `state` INTEGER NOT NULL,
  `created_by` INTEGER DEFAULT NULL,
  `created_date` TEXT DEFAULT NULL,
  `modified_by` INTEGER DEFAULT NULL,
  `modified_date` TEXT DEFAULT NULL
);
