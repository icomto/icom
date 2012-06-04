#!/bin/sh
cat blacklisted_domains.txt | tr '[A-Z]' '[a-z]' | sort | uniq > /tmp/blacklisted_domains.txt.tmp
cp /tmp/blacklisted_domains.txt.tmp blacklisted_domains.txt
