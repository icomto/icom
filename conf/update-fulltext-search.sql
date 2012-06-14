ALTER TABLE forum_posts ENGINE=MyISAM;
ALTER TABLE forum_posts ADD FULLTEXT KEY name (name);
ALTER TABLE forum_posts ADD FULLTEXT KEY content (content);
ALTER TABLE forum_posts ADD FULLTEXT KEY name__content (name, content);
