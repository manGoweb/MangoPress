<?php

function renderCustomAdminPage()
{
	echo '<p>This is custom render with <code>renderCustomAdminPage()</code>. Timestamp: '.date('c').'</p>';
}

function getCustomAdminPageData()
{
	return ['message' => 'data from getCustomAdminPageData(). Timestamp: '.date('c').''];
}

function renderCustomMetabox()
{
	echo '<p>This is custom metabox <code>renderCustomMetabox()</code>. Timestamp: '.date('c').'</p>';
}

function ac_customCallback($post_id, $val)
{
	bdump(['ac callback' => [$post_id, $val]]);
}
