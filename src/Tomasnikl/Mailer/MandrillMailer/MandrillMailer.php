<?php
/**
 * Project: SmartDating.cz.
 *
 * Author: Tomas Nikl <tomasnikl.cz@gmail.com>
 */

namespace Tomasnikl\Mailer\MandrillMailer;


use Nette\DI\Container;
use Nette\Object;
use Nette\Utils\Strings;
use Tomasnikl\Mailer\IMailer;
use Tomasnikl\Mailer\Mailer;
use Latte\Engine;
use Latte\Macros\BlockMacros;
use Latte\Macros\CoreMacros;
use Nette\Bridges\ApplicationLatte\UIMacros;
use Nette\Environment;
use Nette\Utils\DateTime;

class MandrillMailer extends Object implements IMailer {

	public $template;

	public $params = [];

	public $subject;

	public $fromName;

	public $fromEmail;

	public $to = [];

	/**
	 * @var Container
	 */
	private $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function setTemplate($template)
	{
		$this->template = Strings::lower($template);
		return $this;
//		$this->params['part'] = $template;
//		return $this;
	}

	public function setSubject($subject)
	{
		$this->subject = $subject;
		return $this;
	}

	public function setParams(array $params)
	{
		foreach($params as $key => $value) {
			$this->params[] = [
				'name' => $key,
				'content' => $value
			];
//			$this->params[$key] = $value;
		}
		return $this;
	}

	public function setFromName($fromName)
	{
		$this->fromName = $fromName;
		return $this;
	}

	public function setFromEmail($fromEmail)
	{
		$this->fromEmail = $fromEmail;
		return $this;
	}

	public function setTo($to)
	{
		$this->to[] = $to;
		return $this;
	}

	public function getHtmlTemplate()
	{
		$latte = new Engine();
		$this->params['css'] = file_get_contents(WWW_DIR . '/www/resources/css/email.css');
		$this->params['presenter'] = Environment::getApplication()->getPresenter();
		$this->params['_control'] = Environment::getApplication()->getPresenter();
		$this->params['parameters'] = $this->container->parameters;

		$latte->onCompile[] = function(Engine $latte) {
			CoreMacros::install($latte->getCompiler());
			BlockMacros::install($latte->getCompiler());
			UIMacros::install($latte->getCompiler());
		};

		$latte->addFilter('czechDayName', function (DateTime $dateTime) {
			$dayNames = array('neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota');
			return $dayNames[date("w", $dateTime->getTimestamp())];
		});

		return $latte->renderToString(EMAIL_TEMPLATE, $this->params);
	}

	public function send()
	{
		$message = [
//			'html' => $this->getHtmlTemplate(),
			'subject' => $this->subject,
			'from_email' => $this->fromEmail,
			'from_name' => $this->fromName,
			'inline_css' => true,
			'images' => [
				[
					'type' => 'image/png',
					'name' => 'LOGO',
					'content' => base64_encode(file_get_contents(WWW_DIR . '/www/resources/images/logo-email.png'))
				]
			],
			'to' => $this->to,
			'global_merge_vars' => $this->params,
		];

		$mandrill = new \Mandrill($this->container->parameters['mandrill']['apikey']);
		$result = $mandrill->messages->sendTemplate($this->template, [[]], $message, true);
//		$result = $mandrill->messages->send($message, true);
		return $result;
	}


}