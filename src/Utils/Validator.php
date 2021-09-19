<?php

declare(strict_types=1);

namespace App\Utils;

use function checkdate;
use function explode;
use function filter_var;
use function preg_match;
use function preg_replace;
use const FILTER_VALIDATE_EMAIL;

class Validator
{
	private static ?\Ddeboer\Vatin\Validator $vatValidator = null;

	public function validateEmail(string $email, bool $checkMX = false): bool
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
			return false;
		}

		if (!$checkMX) {
			return true;
		}

		[, $domainName] = explode('@', $email, 2);

		return getmxrr($domainName, $mx);
	}

	public function validatePassword(string $password): bool
	{
		//todo
		return true;
	}

	public function validateUrl(string $url): bool
	{
		//todo
		return true;
	}

	public function validateRC(string $rc): bool //@phpcs:ignore Generic.NamingConventions.CamelCapsFunctionName.ScopeNotCamelCaps
	{
		// be liberal in what you receive
		if (!preg_match('#^\s*(\d\d)(\d\d)(\d\d)[ /]*(\d\d\d)(\d?)\s*$#', $rc, $matches)) {
			return false;
		}

		[, $year, $month, $day, $ext, $c] = $matches;

		if ($c === '') {
			$year += $year < 54
				? 1900
				: 1800;
		} else {
			// kontrolní číslice
			$mod = ($year . $month . $day . $ext) % 11;

			if ($mod === 10) {
				$mod = 0;
			}

			if ($mod !== (int) $c) {
				return false;
			}

			$year += $year < 54
				? 2000
				: 1900;
		}

		// k měsíci může být připočteno 20, 50 nebo 70
		if ($month > 70 && $year > 2003) {
			$month -= 70;
		} elseif ($month > 50) {
			$month -= 50;
		} elseif ($month > 20 && $year > 2003) {
			$month -= 20;
		}

		// kontrola data
		return checkdate((int) $month, (int) $day, (int) $year);
	}

	public function validateIc(string $ic): bool
	{
		// be liberal in what you receive
		$ic = preg_replace('#\s+#', '', $ic);

		// má požadovaný tvar?
		if (!preg_match('#^\d{8}$#', $ic)) {
			return false;
		}

		// kontrolní součet
		$a = 0;

		for ($i = 0; $i < 7; ++$i) {
			$a += (int) $ic[$i] * (8 - $i);
		}

		$a %= 11;

		if ($a === 0) {
			$c = 1;
		} elseif ($a === 1) {
			$c = 0;
		} else {
			$c = 11 - $a;
		}

		return (int) $ic[7] === $c;
	}

	public function validateVatNumber(string $vatNumber, bool $onlineValidate = false): bool
	{
		if (!self::$vatValidator instanceof \Ddeboer\Vatin\Validator) {
			self::$vatValidator = new \Ddeboer\Vatin\Validator();
		}

		return self::$vatValidator->isValid($vatNumber, $onlineValidate);
	}
}
