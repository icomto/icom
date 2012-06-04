// -------------------------------------------------------------------
// markItUp!
// -------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// -------------------------------------------------------------------
// Mediawiki Wiki tags example
// -------------------------------------------------------------------
// Feel free to add more tags
// -------------------------------------------------------------------
myWikiSettings = {
	nameSpace:          "wiki",
	previewParserPath:	'', // path to your Wiki parser
	onShiftEnter:		{keepDefault:false, replaceWith:'\n\n'},
	markupSet: [[
		{name:'Heading 1', key:'1', openWith:'== ', closeWith:' ==', placeHolder:'Your title here...' },
		{name:'Heading 2', key:'2', openWith:'=== ', closeWith:' ===', placeHolder:'Your title here...' },
		{name:'Heading 3', key:'3', openWith:'==== ', closeWith:' ====', placeHolder:'Your title here...' },
		{name:'Heading 4', key:'4', openWith:'===== ', closeWith:' =====', placeHolder:'Your title here...' },
		{name:'Heading 5', key:'5', openWith:'====== ', closeWith:' ======', placeHolder:'Your title here...' },
		{separator:'---------------' },		
		{name:'Fett', key:'B', openWith:"'''", closeWith:"'''"}, 
		{name:'Kursiv', key:'I', openWith:"''", closeWith:"''"}, 
		{name:'Durchgestrichen', key:'S', openWith:'<s>', closeWith:'</s>'}, 
		{separator:'---------------' },
		{name:'Liste', openWith:'(!(* |!|*)!)'}, 
		{name:'Numerische Liste', openWith:'(!(# |!|#)!)'}, 
		{separator:'---------------' },
		{name:'Bild', key:"P", replaceWith:'[[Image:[![Url:!:http://]!]|[![name]!]]]'}, 
		{name:'Link', key:"L", openWith:"[[![Link]!]|", closeWith:']', placeHolder:'Your text to link here...' },
		{name:'Url', openWith:"[[![Url:!:http://]!]|", closeWith:']', placeHolder:'Your text to link here...' },
		{separator:'---------------' },
		{name:'Quotes', openWith:'(!(> |!|>)!)', placeHolder:''},
		{name:'Code', openWith:'(!(<source lang="[![Language:!:php]!]">|!|<pre>)!)', closeWith:'(!(</source>|!|</pre>)!)'}, 
		{separator:'---------------' },
		{name:'Vorschau', call:'preview', className:'preview'}
	]]
};