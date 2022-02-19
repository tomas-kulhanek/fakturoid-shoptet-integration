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
			throw new InvalidArgumentException(sprintf('This validator could be used only on text field. You used it on: "%s"', $control::class));
		}
		return $this->validator->validateEmail($control->getValue(), $checkMX);
	}

	public function validateByRegexp(Control $control, string $pattern = '/^.{5}-.{2}-.{3}$/'): bool
	{
		if (!$control instanceof TextInput) {
			throw new InvalidArgumentException(sprintf('This validator could be used only on text field. You used it on: "%s"', $control::class));
		}
		try {
			return Strings::match($control->getValue(), $pattern) !== null;
		} catch (RegexpException) {
			return false;
		}
	}

	public function validateVatNumber(Control $control, bool $params = false): bool
	{
		if (!$control instanceof TextInput) {
			throw new InvalidArgumentException(sprintf('This validator could be used only on text field. You used it on: "%s"', $control::class));
		}
		return $this->validator->validateVatNumber($control->getValue(), $params);
	}

	public function setValidatorMessages(): void
	{
		$validatorMessages = &\Nette\Forms\Validator::$messages;

		$validatorMessages[Controls\CsrfProtection::PROTECTION] = 'messages.formValidation.csrfProtection';
		$validatorMessages[Form::EQUAL] = 'messages.formValidation.equal';
		$validatorMessages[Form::NOT_EQUAL] = 'messages.formValidation.notEqual';
		$validatorMessages[Form::FILLED] = 'messages.formValidation.filled';
		$validatorMessages[Form::BLANK] = 'messages.formValidation.blank';
		$validatorMessages[Form::MIN_LENGTH] = 'messages.formValidation.minLength';
		$validatorMessages[Form::MAX_LENGTH] = 'messages.formValidation.maxLength';
		$validatorMessages[Form::LENGTH] = 'messages.formValidation.length';
		$validatorMessages[Form::EMAIL] = 'messages.formValidation.email';
		$validatorMessages[Form::URL] = 'messages.formValidation.url';
		$validatorMessages[Form::INTEGER] = 'messages.formValidation.integer';
		$validatorMessages[Form::FLOAT] = 'messages.formValidation.float';
		$validatorMessages[Form::MIN] = 'messages.formValidation.min';
		$validatorMessages[Form::MAX] = 'messages.formValidation.max';
		$validatorMessages[Form::RANGE] = 'messages.formValidation.range';
		$validatorMessages[Form::MAX_FILE_SIZE] = 'messages.formValidation.maxFileSize';
		$validatorMessages[Form::MAX_POST_SIZE] = 'messages.formValidation.maxPostSize';
		$validatorMessages[Form::MIME_TYPE] = 'messages.formValidation.mimeType';
		$validatorMessages[Form::IMAGE] = 'messages.formValidation.image';
		$validatorMessages[Controls\SelectBox::VALID] = 'messages.formValidation.selectValid';
		$validatorMessages[Controls\UploadControl::VALID] = 'messages.formValidation.uploadValid';
	}
}
