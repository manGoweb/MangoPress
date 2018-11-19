<?php declare(strict_types = 1);


function set_html_content_type(): string
{
	return 'text/html';
}


function send_mail(string $email, string $subject, string $view, array $parameters = []): void
{
	$content = viewString($view, $parameters);
	add_filter('wp_mail_content_type', 'set_html_content_type');
	wp_mail($email, $subject, $content);
	remove_filter('wp_mail_content_type', 'set_html_content_type');
}
