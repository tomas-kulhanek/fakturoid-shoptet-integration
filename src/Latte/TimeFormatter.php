<?php

declare(strict_types=1);


namespace App\Latte;

class TimeFormatter
{
	public function __invoke(?\DateTimeImmutable $dateTime): string
	{
		if (!$dateTime instanceof \DateTimeImmutable) {
			return '';
		}

		$delta = time() - (int)$dateTime->format('U');

		if ($delta < 0) {
			$delta = round(abs($delta) / 60);
			if ($delta == 0) {
				return 'za okamžik';
			}
			if ($delta == 1) {
				return 'za minutu';
			}
			if ($delta < 45) {
				return 'za ' . $delta . ' ' . $this->plural($delta, 'minuta', 'minuty', 'minut');
			}
			if ($delta < 90) {
				return 'za hodinu';
			}
			if ($delta < 1440) {
				return 'za ' . round($delta / 60) . ' ' . $this->plural(round($delta / 60), 'hodina', 'hodiny', 'hodin');
			}
			if ($delta < 2880) {
				return 'zítra';
			}
			if ($delta < 43200) {
				return 'za ' . round($delta / 1440) . ' ' . $this->plural(round($delta / 1440), 'den', 'dny', 'dní');
			}
			if ($delta < 86400) {
				return 'za měsíc';
			}
			if ($delta < 525960) {
				return 'za ' . round($delta / 43200) . ' ' . $this->plural(round($delta / 43200), 'měsíc', 'měsíce', 'měsíců');
			}
			if ($delta < 1_051_920) {
				return 'za rok';
			}
			return 'za ' . round($delta / 525960) . ' ' . $this->plural(round($delta / 525960), 'rok', 'roky', 'let');
		}

		$delta = round($delta / 60);
		if ($delta == 0) {
			return 'před okamžikem';
		}
		if ($delta == 1) {
			return 'před minutou';
		}
		if ($delta < 45) {
			return "před $delta minutami";
		}
		if ($delta < 90) {
			return 'před hodinou';
		}
		if ($delta < 1440) {
			return 'před ' . round($delta / 60) . ' hodinami';
		}
		if ($delta < 2880) {
			return 'včera';
		}
		if ($delta < 43200) {
			return 'před ' . round($delta / 1440) . ' dny';
		}
		if ($delta < 86400) {
			return 'před měsícem';
		}
		if ($delta < 525960) {
			return 'před ' . round($delta / 43200) . ' měsíci';
		}
		if ($delta < 1_051_920) {
			return 'před rokem';
		}
		return 'před ' . round($delta / 525960) . ' lety';
	}

	/**
	 * Plural: three forms, special cases for 1 and 2, 3, 4.
	 * (Slavic family: Slovak, Czech)
	 */
	private function plural(float $n): mixed
	{
		$args = func_get_args();
		return $args[($n == 1) ? 1 : (($n >= 2 && $n <= 4) ? 2 : 3)];
	}
}
