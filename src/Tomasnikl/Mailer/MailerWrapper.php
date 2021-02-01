<?php
/**
 * Project: SmartDating.cz.
 *
 * Author: Tomas Nikl <tomasnikl.cz@gmail.com>
 */

namespace Tomasnikl\Mailer;

use Nette\DI\Container;

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