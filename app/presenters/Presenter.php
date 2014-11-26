<?php

namespace App\Presenters;

use App\Controls\FormControl;
use App\Models\Orm\RepositoryContainer;
use Nette\Application\UI\Presenter as NPresenter;
use Nette\Bridges\ApplicationLatte\Template;


/**
 * @property-read Template $template
 */
abstract class Presenter extends NPresenter
{

	const FLASH_INFO = 'info';
	const FLASH_SUCCESS = 'success';
	const FLASH_WARNING = 'warning';
	const FLASH_ERROR = 'danger';


	/**
	 * @var RepositoryContainer
	 * @inject
	 */
	public $repos;

	protected function createComponent($name)
	{
		if (substr($name, -4) === 'Form')
		{
			$formClass = 'App\\Controls\\Forms\\' . ucFirst(substr($name, 0, -4));
			if (class_exists($formClass))
			{
				return $this->context->createInstance(FormControl::class, [$formClass]);
			}
		}
		else
		{
			$controlClass = 'App\\Controls\\' . ucFirst($name);
			if (class_exists($controlClass))
			{
				return $this->context->createInstance($controlClass);
			}
		}
		return parent::createComponent($name);
	}

	public function flashInfo($headline, $message)
	{
		$this->flashMessage($headline, $message, self::FLASH_INFO);
	}

	public function flashSuccess($headline, $message)
	{
		$this->flashMessage($headline, $message, self::FLASH_SUCCESS);
	}

	public function flashWarning($headline, $message)
	{
		$this->flashMessage($headline, $message, self::FLASH_WARNING);
	}

	public function flashError($headline, $message)
	{
		$this->flashMessage($headline, $message, self::FLASH_ERROR);
	}

	/**
	 * @param string $headline
	 * @param NULL|string $message
	 * @param NULL|string $type
	 * @return \stdClass
	 */
	public function flashMessage($headline, $message = NULL, $type = self::FLASH_INFO)
	{
		$id = $this->getParameterId('flash');
		$messages = $this->getFlashSession()->$id;
		$messages[] = $flash = (object) [
			'headline' => $headline,
			'message' => $message,
			'type' => $type,
		];
		$this->template->flashes = $messages;
		$this->getFlashSession()->$id = $messages;
		return $flash;
	}

	public function formatTemplateFiles()
	{
		$name = $this->getName();
		$root = $this->context->getParameters()['appDir'] . '/templates/views';
		return [
			"$root/" . implode('/', explode(':', $name)) . "/$this->view.latte",
		];
	}

	public function formatLayoutTemplateFiles()
	{
		$name = $this->getName();
		$presenter = substr($name, strrpos(':' . $name, ':'));
		$layout = $this->layout ? $this->layout : 'layout';
		$dir = dirname($this->getReflection()->getFileName());
		$dir = is_dir("$dir/templates/views") ? $dir : dirname($dir);
		$list = [
			"$dir/templates/views/$presenter/@$layout.latte",
			"$dir/templates/views/$presenter.@$layout.latte",
		];
		do {
			$list[] = "$dir/templates/views/@$layout.latte";
			$dir = dirname($dir);
		} while ($dir && ($name = substr($name, 0, strrpos($name, ':'))));
		return $list;
	}

}
