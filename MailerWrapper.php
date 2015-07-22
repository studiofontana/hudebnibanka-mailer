<?php
/**
 * Project: SmartDating.cz.
 *
 * Author: Tomas Nikl <tomasnikl.cz@gmail.com>
 */

namespace tomasnikl\Mailer;


use Nette\DI\Container;
use Nette\Object;
use tomasnikl\Mailer\IMailer;

class MailerWrapper {

	/**
	 * @var IMailer
	 */
	public $mailer;

	/**
	 * @var Container
	 */
	private $container;

	public function __construct(Container $container, IMailer $mailer)
	{
		$this->container = $container;
		$this->mailer = $mailer;
	}

	/**
	 * @return IMailer
	 */
	public function getMailer()
	{
		return $this->mailer;
	}
} 