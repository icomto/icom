ALTER TABLE forum_posts DROP KEY name;
ALTER TABLE forum_posts DROP KEY content;
ALTER TABLE forum_posts DROP KEY name__content;
ALTER TABLE forum_posts ENGINE=InnoDB;
