<?php
/**
 * Project: SmartDating.cz.
 *
 * Author: Tomas Nikl <tomasnikl.cz@gmail.com>
 */

namespace Tomasnikl\Mailer\NetteMailer;


use Nette\DI\Container;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Object;
use Tomasnikl\Mailer\IMailer;
use Latte\Engine;
use Latte\Macros\BlockMacros;
use Latte\Macros\CoreMacros;
use Nette\Bridges\ApplicationLatte\UIMacros;
use Nette\Environment;
use Nette\Utils\DateTime;

class NetteMailer extends Object implements IMailer {
	public $template;

	public $params = ['fromMandrill' => false];

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
		$this->template = $template;
		$this->params['part'] = $template;
		return $this;
	}

	public function setSubject($subject)
	{
		$this->subject = $subject;
		return $this;
	}

	public function setParams(array $params)
	{
		foreach($params as $key => $value) {
			$this->params[$key] = $value;
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
		$mail = new Message;

		if($this->fromName && $this->fromEmail)
		{
			$from = $this->fromName . ' <' . $this->fromEmail . '>';
		}else{
			$from = $this->fromEmail;
		}

		$mail = $mail->setFrom($from);
		foreach($this->to as $to)
		{
			$mail = $mail->addTo($to['email']);
		}
		$mail->setSubject($this->subject);
		$mail->setHtmlBody($this->getHtmlTemplate());

		$mailer = new SendmailMailer;
		$mailer->send($mail);
	}

} 