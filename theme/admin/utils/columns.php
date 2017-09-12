<?php

addCustomColumn('page', 'example-column', 'Example column', function($id) {
	echo "Content for page $id";
});
