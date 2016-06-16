<?php

/**
 * Test: block types compatibility
 */

use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test(function () {
	$latte = new Latte\Engine;
	$latte->setLoader(new Latte\Loaders\StringLoader);

	Assert::same(
		'<meta content="b&quot;ar&quot;&lt;&gt;&amp;">b&quot;ar"<>&amp;',
		$latte->renderToString('<meta content="{include foo}">{block foo}{$value}"<>&amp;{/block}', ['value' => 'b"ar'])
	);

	Assert::error(function () use ($latte) {
		$latte->renderToString('<meta content={include foo}>{block foo}{$value}{/block}', ['value' => 'b"ar']);
	}, E_USER_WARNING, 'Including block foo with content type HTML into incompatible type HTMLTAG.');

	Assert::same(
		'<meta content=b&quot;ar>b&quot;ar',
		$latte->renderToString('<meta content={include foo|noescape}>{block foo}{$value}{/block}', ['value' => 'b"ar'])
	);

	Assert::same(
		'<meta content="b&quot;ar&quot;"><meta content="b&quot;ar&quot;">',
		$latte->renderToString('<meta content="{block foo}{$value}&quot;{/block}"><meta content="{include foo}">', ['value' => 'b"ar'])
	);

	Assert::same(
		'<a href="b&quot;ar"><meta content="b&quot;ar">',
		$latte->renderToString('<a href="{block foo}{$value}{/block}"><meta content="{include foo}">', ['value' => 'b"ar'])
	);

	Assert::same(
		'<meta content="b&quot;ar"><meta content="b&quot;ar">',
		$latte->renderToString('<meta content={block foo}{$value}{/block}><meta content={include foo}>', ['value' => 'b"ar'])
	);

	Assert::same(
		'foo<div>foo</div>',
		$latte->renderToString('{block main}foo{/block}<div>{include main}</div>')
	);

	Assert::same(
		'<div>foo</div><div>foo</div>',
		$latte->renderToString('{include main}{block main}<div>foo</div>{/block}')
	);
});


test(function () {
	$latte = new Latte\Engine;
	$latte->setLoader(new Latte\Loaders\StringLoader([
		'parent' => '<meta name={block foo}{/block}>',

		'parentvar' => '{var $name = "foo"}<meta name={block $name}{/block}>',

		'context1' => '
{extends "parent"}
<meta name={block foo}{$foo}{/block}>
		',

		'context2' => '
{extends "parentvar"}
<meta name={block foo}{$foo}{/block}>
		',

		'context3' => '
{extends "parent"}
{block foo}{$foo}{/block}
		',

		'context4' => '
{extends "parentvar"}
{block foo}{$foo}{/block}
		',

		'context5' => '
{extends "parent"}
<meta name="{block foo}{$foo}{/block}">
		',

		'context6' => '
{extends "parentvar"}
<meta name="{block foo}{$foo}{/block}">
		',

		'parentattr' => '<meta name="{block foo}{/block}">',

		'context7' => '
{extends "parentattr"}
{block foo}{$foo}{/block}
		',
	]));

	Assert::match('<meta name="b&quot;ar">', $latte->renderToString('context1', ['foo' => 'b"ar']));

	Assert::match('<meta name="b&quot;ar">', $latte->renderToString('context2', ['foo' => 'b"ar']));

	Assert::error(function () use ($latte) {
		$latte->renderToString('context3', ['foo' => 'b"ar']);
	}, E_USER_WARNING, 'Overridden block foo with content type HTMLTAG by incompatible type HTML.');

	Assert::error(function () use ($latte) {
		$latte->renderToString('context4', ['foo' => 'b"ar']);
	}, E_USER_WARNING, 'Overridden block foo with content type HTMLTAG by incompatible type HTML.');

	Assert::error(function () use ($latte) {
		$latte->renderToString('context5', ['foo' => 'b"ar']);
	}, E_USER_WARNING, 'Overridden block foo with content type HTMLTAG by incompatible type HTMLATTR.');

	Assert::error(function () use ($latte) {
		$latte->renderToString('context6', ['foo' => 'b"ar']);
	}, E_USER_WARNING, 'Overridden block foo with content type HTMLTAG by incompatible type HTMLATTR.');

	Assert::error(function () use ($latte) {
		$latte->renderToString('context7', ['foo' => 'b"ar']);
	}, E_USER_WARNING, 'Overridden block foo with content type HTMLATTR by incompatible type HTML.');
});


test(function () {
	$latte = new Latte\Engine;
	$latte->setLoader(new Latte\Loaders\StringLoader);

	Assert::match(
		"<div><h1>title</h1></div> <h1>title</h1>",
		$latte->renderToString('<div>{include title}</div> <h1 n:block=title>title</h1>')
	);

	Assert::match(
		"<div><style>...</style></div> <style>...</style>",
		$latte->renderToString('<div>{include style}</div> <style n:block=style>...</style>')
	);

	Assert::error(function () use ($latte) {
		$latte->renderToString('<div>{include style}</div> <STYLE n:inner-block=style>...</STYLE>');
	}, E_USER_WARNING, 'Including block style with content type HTMLCSS into incompatible type HTML.');

	Assert::match(
		"<div><script>...</script></div> <script>...</script>",
		$latte->renderToString('<div>{include script}</div> <script n:block=script>...</script>')
	);

	Assert::error(function () use ($latte) {
		$latte->renderToString('<div>{include script}</div> <script n:inner-block=script>...</script>');
	}, E_USER_WARNING, 'Including block script with content type HTMLJS into incompatible type HTML.');

});


$latte = new Latte\Engine;
$latte->setLoader(new Latte\Loaders\StringLoader([
	'ical.latte' => '{contentType text/calendar; charset=utf-8} <>',

	'context1' => '<p>{include ical.latte}</p>',
	'context2' => '{extends ical.latte}',
	'context3' => 'x{includeblock ical.latte}',
	'context4' => '{contentType calendar} {include ical.latte}',
	'context5' => '<p>{include ical.latte|noescape}</p>',
	'context6' => '{contentType javascript} {include ical.latte}',
]));

Assert::error(function () use ($latte) {
	$latte->renderToString('context1');
}, E_USER_WARNING, "Including 'ical.latte' with content type ICAL into incompatible type HTML.");

Assert::same(' <>', $latte->renderToString('context2'));

Assert::error(function () use ($latte) {
	$latte->renderToString('context3');
}, E_USER_WARNING, "Including 'ical.latte' with content type ICAL into incompatible type HTML.");

Assert::same(' <>', $latte->renderToString('context4'));

Assert::same('<p> <></p>', $latte->renderToString('context5'));

Assert::error(function () use ($latte) {
	$latte->renderToString('context6');
}, E_USER_WARNING, "Including 'ical.latte' with content type ICAL into incompatible type JS.");



$latte = new Latte\Engine;
$latte->setLoader(new Latte\Loaders\StringLoader([
	'js.latte' => '{contentType javascript} </script>',

	'context1' => '<p>{include js.latte}</p>',
	'context2' => '<p title="{include js.latte}"></p>',
	'context3' => '<p title={include js.latte}></p>',
	'context4' => '<script>{include js.latte}</script>',
	'context5' => '<style>{include js.latte}</style>',
]));

Assert::same('<p> &lt;/script&gt;</p>', $latte->renderToString('context1'));

Assert::same('<p title=" &lt;/script&gt;"></p>', $latte->renderToString('context2'));

Assert::error(function () use ($latte) {
	$latte->renderToString('context3');
}, E_USER_WARNING, "Including 'js.latte' with content type JS into incompatible type HTMLTAG.");

Assert::same('<script> <\/script></script>', $latte->renderToString('context4'));

Assert::error(function () use ($latte) {
	$latte->renderToString('context5');
}, E_USER_WARNING, "Including 'js.latte' with content type JS into incompatible type HTMLCSS.");



$latte = new Latte\Engine;
$latte->setLoader(new Latte\Loaders\StringLoader([
	'html.latte' => '<hr> " &quot;',

	'context1' => '<p>{include html.latte}</p>',
	'context1a' => '<p>{include html.latte|noescape}</p>',
	'context1b' => '<p>{include html.latte|stripHtml|upper}</p>',
	'context1c' => '<p>{include html.latte|stripHtml|upper|noescape}</p>',
	'context2' => '<p title="{include html.latte}"></p>',
	'context2a' => '<p title="{include html.latte|noescape}"></p>',
	'context2b' => '<p title="{include html.latte|stripHtml|upper}"></p>',
	'context2c' => '<p title="{include html.latte|stripHtml|upper|noescape}"></p>',
	'context3' => '<p title={include html.latte}></p>',
	'context4' => '<script>{include html.latte}</script>',
	'context5' => '<style>{include html.latte}</style>',
]));

Assert::same('<p><hr> " &quot;</p>', $latte->renderToString('context1'));

Assert::same('<p><hr> " &quot;</p>', $latte->renderToString('context1a'));
Assert::same('<p> &quot; &quot;</p>', $latte->renderToString('context1b'));
Assert::same('<p> &quot; &quot;</p>', $latte->renderToString('context1c'));

Assert::same('<p title="&lt;hr&gt; &quot; &quot;"></p>', $latte->renderToString('context2'));

Assert::same('<p title="<hr> " &quot;"></p>', $latte->renderToString('context2a'));
Assert::same('<p title=" &quot; &quot;"></p>', $latte->renderToString('context2b'));
Assert::same('<p title=" &quot; &quot;"></p>', $latte->renderToString('context2c'));

Assert::error(function () use ($latte) {
	$latte->renderToString('context3');
}, E_USER_WARNING, "Including 'html.latte' with content type HTML into incompatible type HTMLTAG.");

Assert::error(function () use ($latte) {
	$latte->renderToString('context4');
}, E_USER_WARNING, "Including 'html.latte' with content type HTML into incompatible type HTMLJS.");

Assert::error(function () use ($latte) {
	$latte->renderToString('context5');
}, E_USER_WARNING, "Including 'html.latte' with content type HTML into incompatible type HTMLCSS.");
