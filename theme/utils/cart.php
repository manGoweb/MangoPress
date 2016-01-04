<?php

return; // remove if you want to use Cart

require __DIR__ .'/shop/cart.php';

$App->cart = new MangoPress\Shop\Cart($App->session);
