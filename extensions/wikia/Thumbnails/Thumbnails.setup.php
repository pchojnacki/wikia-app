<?
$wgExtensionCredits['videohandlers'][] = [
	'name' => 'Thumbnails',
	'author' => [
		"Liz Lee <liz at wikia-inc.com>",
		"Saipetch Kongkatong <saipetch at wikia-inc.com>",
		"Garth Webb <garth at wikia-inc.com>",
		"Kenneth Kouot <kenneth at wikia-inc.com>",
		"James Sutterfield <james at wikia-inc.com>",
	],
	'descriptionmsg' => 'thumbnails-extension-description',
];

$dir = dirname( __FILE__ );

// Main classes
$wgAutoloadClasses['ThumbnailVideo'] = $dir . '/ThumbnailVideo.class.php';
$wgAutoloadClasses['ThumbnailVideoController'] = $dir . '/ThumbnailVideoController.class.php';
$wgAutoloadClasses['ThumbnailImageController'] = $dir . '/ThumbnailImageController.class.php';

