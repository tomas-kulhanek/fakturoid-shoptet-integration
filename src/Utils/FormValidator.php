<?php

declare(strict_types=1);

namespace App\Utils;

use Nette\Forms\Control;
use Nette\Forms\Controls;
use Nette\Forms\Controls\TextInput;
use Nette\Forms\Form;
use Nette\InvalidArgumentException;
use Nette\Utils\RegexpException;
use Nette\Utils\Strings;

class FormValidator implements IFormValidator
{
	private Validator $validator;

	public function __construct(Validator $validator)
	{
		$this->validator = $validator;
	}

	public function validateIco(Control $control): bool
	{
		return $this->validator->validateIc($control->getValue());
	}

	public function validateUrl(Control $control): bool
	{
		return $this->validator->validateUrl($control->getValue());
	}

	public function validateRc(Control $control): bool
	{
		return $this->validator->validateRC($control->getValue());
	}

	public function validateEmail(Control $control, bool $checkMX = false): bool
	{
		if (!$control instanceof TextInput) {
			throw new InvalidArgumentException(sprintf('This validator could be used only on text field. You used it on: "%s"', get_class($control)));
		}
		return $this->validator->validateEmail($control->getValue(), $checkMX);
	}

	public function validateByRegexp(Control $control, string $pattern = '/^.{5}-.{2}-.{3}$/'): bool
	{
		if (!$control instanceof TextInput) {
			throw new InvalidArgumentException(sprintf('This validator could be used only on text field. You used it on: "%s"', get_class($control)));
		}
		try {
			return !empty(Strings::match($control->getValue(), $pattern));
		} catch (RegexpException $exception) {
			return false;
		}
	}

	public function validateVatNumber(Control $control, bool $params = false): bool
	{
		if (!$control instanceof TextInput) {
			throw new InvalidArgumentException(sprintf('This validator could be used only on text field. You used it on: "%s"', get_class($control)));
		}
		return $this->validator->validateVatNumber($control->getValue(), $params);
	}

	public function setValidatorMessages(): void
	{
		$validatorMessages = &\Nette\Forms\Validator::$messages;

		$validatorMessages[Controls\CsrfProtection::PROTECTION] = 'core.formValidation.csrfProtection';
		$validatorMessages[Form::EQUAL] = 'core.formValidation.equal';
		$validatorMessages[Form::NOT_EQUAL] = 'core.formValidation.notEqual';
		$validatorMessages[Form::FILLED] = 'core.formValidation.filled';
		$validatorMessages[Form::BLANK] = 'core.formValidation.blank';
		$validatorMessages[Form::MIN_LENGTH] = 'core.formValidation.minLength';
		$validatorMessages[Form::MAX_LENGTH] = 'core.formValidation.maxLength';
		$validatorMessages[Form::LENGTH] = 'core.formValidation.length';
		$validatorMessages[Form::EMAIL] = 'core.formValidation.email';
		$validatorMessages[Form::URL] = 'core.formValidation.url';
		$validatorMessages[Form::INTEGER] = 'core.formValidation.integer';
		$validatorMessages[Form::FLOAT] = 'core.formValidation.float';
		$validatorMessages[Form::MIN] = 'core.formValidation.min';
		$validatorMessages[Form::MAX] = 'core.formValidation.max';
		$validatorMessages[Form::RANGE] = 'core.formValidation.range';
		$validatorMessages[Form::MAX_FILE_SIZE] = 'core.formValidation.maxFileSize';
		$validatorMessages[Form::MAX_POST_SIZE] = 'core.formValidation.maxPostSize';
		$validatorMessages[Form::MIME_TYPE] = 'core.formValidation.mimeType';
		$validatorMessages[Form::IMAGE] = 'core.formValidation.image';
		$validatorMessages[Controls\SelectBox::VALID] = 'core.formValidation.selectValid';
		$validatorMessages[Controls\UploadControl::VALID] = 'core.formValidation.uploadValid';
	}
}
