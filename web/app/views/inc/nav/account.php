<? $current = Request::segment(1); $i = 0; $last = ''; foreach($vData['accountNav'] as $key => $val):
	$i++; if($i == count($vData['accountNav'])) $last = ' last';
	$compare = explode('/',$key)[1];
	$ref = ' link'.$compare;
	$active = ''; if( $current == $compare) $active = 'active'; ?>
	<li<?= ' class="'.$active.$last.$ref.'" ' ?> >
		<a href="<?= $key ?>"><?= $val ?></a>
	</li>
<? endforeach; ?>