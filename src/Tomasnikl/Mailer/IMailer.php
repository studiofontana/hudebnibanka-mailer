<?php
/**
 * Project: SmartDating.cz.
 *
 * Author: Tomas Nikl <tomasnikl.cz@gmail.com>
 */

namespace Tomasnikl\Mailer;


interface IMailer {

	/**
	 * @param $template
	 * @return IMailer
	 */
	public function setTemplate($template);

	/**
	 * @param $subject
	 * @return IMailer
	 */
	public function setSubject($subject);

	/**
	 * @param array $params
	 * @return IMailer
	 */
	public function setParams(array $params);

	/**
	 * @param $fromName
	 * @return IMailer
	 */
	public function setFromName($fromName);

	/**
	 * @param $fromEmail
	 * @return IMailer
	 */
	public function setFromEmail($fromEmail);

	/**
	 * @param $to
	 * @return IMailer
	 */
	public function setTo($to);

	/**
	 * @return mixed
	 */
	public function send();

} 