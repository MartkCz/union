<?php

/**
 * Test: Tracy\Dumper::toHtml() with location
 *
 * @author     David Grudl
 */

use Tracy\Dumper,
	Tester\Assert;


require __DIR__ . '/../bootstrap.php';


class Test
{
}

Assert::match( '<pre class="tracy-dump" title="Dumper::toHtml( new Test, array(&quot;location&quot; =&gt; TRUE) ) )
in file %a% on line %d%"><span class="tracy-dump-object" data-tracy-href="editor:%a%">Test</span> <span class="tracy-dump-hash">#%h%</span>
<small>in <a href="editor:%a%">%a%:%d%</a></small></pre>
', Dumper::toHtml( new Test, array("location" => TRUE) ) );
