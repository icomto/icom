delete b from user_pns3 a, user_pns3_content b where find_in_set(1, a.involved_users) and b.subid=a.pn_id;
delete b from user_pns3 a, user_pns3_links b where find_in_set(1, a.involved_users) and b.pn_id=a.pn_id;
delete b from user_pns3 a, user_pns3_new b where find_in_set(1, a.involved_users) and b.pn_id=a.pn_id;
delete b from user_pns3 a, user_pns3_polls b where find_in_set(1, a.involved_users) and b.pn_id=a.pn_id;
delete a from user_pns3 a where find_in_set(1, a.involved_users);
