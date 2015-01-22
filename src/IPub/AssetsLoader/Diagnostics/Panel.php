<?php
/**
 * Panel.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Diagnostics
 * @since		5.0
 *
 * @date		16.01.15
 */

namespace IPub\AssetsLoader\Diagnostics;

use Nette;
use Nette\Application;

use Latte;
use Latte\Runtime;

use Tracy;

final class Panel extends Nette\Object implements Tracy\IBarPanel
{
	/**
	 * @var string
	 */
	public static $icon = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAJ+SURBVBgZBcExbFRlAADg7//fu7teC3elQEoMgeDkYDQ6oMQQTYyGxMHZuDA6Ypw0cWI20cHJUdl0cJLIiomR6OACGhUCpqGWtlzbu/b97/3v9/tCKQVc/e7RRXz+7OrSpUXbW7S9tu8ddv0M+3iCjF1s42v8WAP0XffKi2eOXfro9dMAYJ766SL1092jfDa17DfZgycHfvh7/hau1QB9161PhgE8epoNQlAHqprRIDo3iqoYDSpeOjv2zHRl7atfNj6LALltJys1Xc9+CmYtTxtmR8yO2D7kv4MMPr7x0KULK54/NThdA+S2XTs+jOYN86MsxqBGVRErKkEV6BHynp//2fXbw9lGDZBTWp+OK7PDzqIpYiyqSMxBFakUVYVS2dxrfHHrrz1crQG6lM6vTwZmR0UHhSoHsSBTKeoS9YU8yLrUXfj+w9d2IkBOzfkz05F5KkKkCkFERACEQil0TSOnJkMNV67fHNdVHI4GUcpZVFAUZAEExEibs4P5osMeROiadHoUiIEeCgFREAoRBOMB2weNrkmbNz+9UiBCTs1yrVdHqhgIkRL0EOj7QGG5jrZ2D+XUbADEy9dunOpSun7xuXMe7xUPNrOd/WyeyKUIoRgOGS8xWWZ7b6FLaROgzim9iXd+vXvf7mHtoCnaXDRtkLpel3t9KdamUx+8fcbj7YWc0hZAndv25XffeGH8yfuvAoBcaHOROhS+vLlhecD+wUJu222AOrft/cdPZr65ddfqsbHVyZLVlZHpysjx5aHRMBrV0XuX141qtnb25bb9F6Duu+7b23funb195955nMRJnMAJTJeGg8HS0sBkZWx1suz3Px79iZ8A/gd7ijssEaZF9QAAAABJRU5ErkJggg==";

	/**
	 * @var array
	 */
	private $files = [];

	/**
	 * @var Application\Application
	 */
	private $application;

	public function __construct(Application\Application $application)
	{
		$this->application = $application;
	}

	public function register()
	{
		Tracy\Debugger::getBar()->addPanel($this, 'ipub.assetsLoader');
	}

	public function addFile($source, $id, $type, $lastModified, $memory = NULL)
	{
		if (is_array($source)) {
			foreach ($source as $file) {
				$this->files[$file]=[
					'id'			=> $id,
					'type'			=> $type,
					'memory'		=> $memory,
					'lastModified'	=> $lastModified
				];
			}

		} else {
			$this->files[$source]=[
				'id'			=> $id,
				'type'			=> $type,
				'memory'		=> $memory,
				'lastModified'	=> $lastModified
			];
		}
	}

	private function link($file, $type, $timestamp)
	{
		$link = $this->getPresenter()->link(':IPub:AssetsLoader:assets', ['type' => $type, 'id' => $file, 'timestamp' => $timestamp]);
		$name = str_replace(WWW_DIR, '', $file);

		return '<a href="'.$link.'" target="_blank">'.$name.'</a>';
	}

	private function getPresenter()
	{
		return $this->application->getPresenter();
	}

	/*** IDebugPanel ***/

	public function getTab()
	{
		return '<span><img src="'.self::$icon.'">AssetsLoader ('.count($this->files).')</span>';
	}

	public function getPanel()
	{
		$buff = '<h1>AssetsLoader</h1>'
			.'<div class="nette-inner">'
			.'<table>'
			.'<thead><tr><th>Source</th><th>Generated file</th><th>Memory usage</th></tr></thead>';

		$i=0;

		foreach ($this->files as $source => $generated) {
			$buff.='<tr><th'.($i%2? 'class="nette-alt"' : '').'>'
				. $source
				.'</th><td>'
				. $this->link($generated['id'], $generated['type'], $generated['lastModified'])
				.'</td><td>'
				. Runtime\Filters::bytes($generated['memory'])
				.'</td></tr>';
		}

		return $buff .'</table></div>';
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return 'ipub.assetsLoader';
	}
}