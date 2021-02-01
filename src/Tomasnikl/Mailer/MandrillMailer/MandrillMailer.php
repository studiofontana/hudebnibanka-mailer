<?php
/**
 * Project: SmartDating.cz.
 *
 * Author: Tomas Nikl <tomasnikl.cz@gmail.com>
 */

namespace Tomasnikl\Mailer\MandrillMailer;

use Nette\DI\Container;
use Nette\SmartObject;
use Nette\Utils\Strings;
use Tomasnikl\Mailer\IMailer;

class MandrillMailer implements IMailer {

    use SmartObject;

	public $template;

	public $params = [];

	public $subject;

	public $fromName;

	public $fromEmail;

	public $to = [];

	public $attachments = [];

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

	public function setAttachment($attachment)
	{
		$this->attachments[] = $attachment;
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
		// nastaveni, at chodi maily na ten nastaveny v configu pokud je vyplnen (na testu)
		$parameters = $this->container->parameters;
		if(isset($parameters['testEmail']) && $parameters['testEmail']) {
			if(!is_array($to)) {
				$to = $parameters['testEmail'];
			}else{
				foreach($to as $key => $value) {
					$to[$key] = $parameters['testEmail'];
				}
			}
		}
		$this->to[] = $to;
		return $this;
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

		if(count($this->attachments)) {
			$message['attachments'] = $this->attachments;
		}

		$mandrill = new \Mandrill($this->container->parameters['mandrill']['apikey']);
		$result = $mandrill->messages->sendTemplate($this->template, [[]], $message, true);
//		$result = $mandrill->messages->send($message, true);
		return $result;
	}


}