<?php

class Mock {
	static function image($size = 600) {
		$sizes = explode('/', (string) $size);
		if(count($sizes) === 1) {
			$sizes[1] = $size;
		}

		return 'https://unsplash.it/' . implode('/', $sizes) . '?random=' . rand();
	}

	static function text($len = 300) {
		return Nette\Utils\Strings::truncate('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam quis porttitor nisl. Nullam feugiat euismod nisi at condimentum. Phasellus pulvinar dui non commodo efficitur. Maecenas dapibus mi sit amet ligula varius, non blandit ipsum tempus. Vestibulum suscipit urna sed nunc fringilla, vel consectetur felis posuere. Mauris pellentesque risus vel libero viverra ultricies. Aliquam hendrerit feugiat lacus sed ultrices. Vestibulum convallis enim id nibh mollis semper.

Cras a efficitur tellus. Duis mattis finibus augue at bibendum. Curabitur suscipit nisl ut est volutpat, id dignissim purus efficitur. Donec pellentesque nisi sit amet gravida commodo. Pellentesque porta eros sit amet elit porttitor, id semper purus luctus. Nunc sit amet rutrum lorem, at mattis enim. Pellentesque enim risus, efficitur sit amet aliquet in, tempor nec est.

Maecenas euismod metus id tellus sodales viverra. Proin a neque in metus tincidunt egestas. Donec in interdum felis, non eleifend tortor. Nunc a erat turpis. Phasellus congue viverra tellus, sed vulputate metus ultrices a. Curabitur a justo fringilla, semper augue ut, efficitur nisi. Nunc ultrices sed lorem vitae scelerisque.

Donec lobortis dolor sem, non ornare enim sodales a. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis imperdiet dolor. Phasellus eleifend sed mi sit amet faucibus. Phasellus lobortis tincidunt convallis. Ut felis felis, dignissim ac tempus non, fringilla a orci. Sed lacinia mauris nulla, sed tempor metus mattis vitae. Aliquam sagittis orci mauris, a ultrices sapien molestie sit amet. Nunc elementum sit amet lacus sed condimentum. Fusce sed ipsum a enim fermentum lacinia. Aenean faucibus ipsum a ullamcorper rutrum.

Sed blandit interdum bibendum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum viverra feugiat felis eu sollicitudin. Nullam enim mauris, ultricies nec risus sed, mattis scelerisque ex. Quisque nec auctor augue. Proin porttitor commodo mi, vitae maximus mauris semper ut. Mauris blandit quis nisl ac dignissim. Vivamus cursus, dui at molestie dignissim, elit elit mattis dui, vitae fermentum nunc orci at ligula.', $len);
	}
}
