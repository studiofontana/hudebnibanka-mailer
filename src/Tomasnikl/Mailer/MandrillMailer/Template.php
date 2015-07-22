<?php
/**
 * Project: SmartDating.cz.
 *
 * Author: Tomas Nikl <tomasnikl.cz@gmail.com>
 */

namespace Tomasnikl\Mailer\MandrillMailer;


use Latte\Engine;
use Latte\Macros\BlockMacros;
use Latte\Macros\CoreMacros;
use Nette\Bridges\ApplicationLatte\UIMacros;
use Nette\DI\Container;
use Nette\Environment;
use Nette\Utils\DateTime;

class Template {

	/**
	 * @var Container
	 */
	private $container;

	private $data = [];

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * @param $template
	 * @return $this
	 */
	public function setTemplate($template)
	{
		$this->data['part'] = $template;
		return $this;
	}

	/**
	 * @param array $params
	 * @return $this
	 */
	public function setParams(array $params)
	{
		foreach($params as $key => $value) {
			$this->data[$key] = $value;
		}
		return $this;
	}

	/**
	 * @return string
	 */
	public function toString()
	{
		$latte = new Engine();
		$this->data['css'] = file_get_contents(WWW_DIR . '/www/resources/css/email.css');
		$this->data['presenter'] = Environment::getApplication()->getPresenter();
		$this->data['_control'] = Environment::getApplication()->getPresenter();
		$this->data['parameters'] = $this->container->parameters;

		$latte->onCompile[] = function(Engine $latte) {
			CoreMacros::install($latte->getCompiler());
			BlockMacros::install($latte->getCompiler());
			UIMacros::install($latte->getCompiler());
		};

		$latte->addFilter('czechDayName', function (DateTime $dateTime) {
			$dayNames = array('neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota');
			return $dayNames[date("w", $dateTime->getTimestamp())];
		});

		return $latte->renderToString(EMAIL_TEMPLATE, $this->data);
	}
} 