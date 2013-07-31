Swap History

<ul class="lvs-undo-list">
<? foreach ( $videos as $video ): ?>
	<li>
		<?= $video['createDate'] ?>
		[<a href="<?= $video['userLink'] ?>"><?= $video['userName'] ?></a>]
		<? if ( $video['statusSwap'] ): ?>
			<?= wfMessage( 'lvs-history-swapped', $video['titleLink'], $video['newTitleLink'] )->plain() ?>
		<? else: ?>
			<?= wfMessage( 'lvs-history-kept', $video['titleLink'] )->plain() ?>
		<? endif; ?>
		(<a class="undo-link" href="<?= $video['undo'] ?>">undo</a>)
	</li>
<? endforeach; ?>
</ul>