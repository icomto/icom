#!/bin/sh
files="
	jquery/superfish.js
	jquery/jSize.js
	jquery/jquery.tooltip.js

	lightbox-0.5/jquery.lightbox-0.5.js

	markitup/jquery.markitup.js
	markitup/bbcode.js
	markitup/wiki.js

	engine.js"

md5=$(cat $files | md5sum | sed 's/\s-//g')

if [ ! -f "vt-hash" ] || [ "$(cat vt-hash)" != "`echo -n $md5`" ] ; then
	cat $files > /tmp/out.js
	rm -f "$(ls | grep ^vt-[0-9a-f]*\.js$)"
	echo -n $md5 >vt-hash
	yui-compressor --type js -o "vt-$(echo $md5).js" "/tmp/out.js"
	rm -f "/tmp/out.js"
	echo "Created vt-$(echo $md5).js"
fi
