<?php

register_theme_directory(BASE_DIR);

add_action('plugins_loaded', 'search_theme_directories');
