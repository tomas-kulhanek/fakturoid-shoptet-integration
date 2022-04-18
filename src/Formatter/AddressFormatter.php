<?php

declare(strict_types=1);


namespace App\Formatter;

use App\UI\Address\AddressInterface;
use CommerceGuys\Addressing\Address;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepository;
use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Formatter\DefaultFormatter;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use Nette\Localization\Translator;

class AddressFormatter
{
	private DefaultFormatter $formatter;

	public function __construct(
		protected Translator $translator
	) {
		$addressFormatRepository = new AddressFormatRepository();
		$countryRepository = new CountryRepository('cs', 'cs');
		$subdivisionRepository = new SubdivisionRepository($addressFormatRepository);
		$this->formatter = new DefaultFormatter($addressFormatRepository, $countryRepository, $subdivisionRepository, ['locale' => 'cs']);
	}

	public function format(?AddressInterface $originalAddress, bool $html = true, ?string $registrationNo = null, ?string $vatId = null): string
	{
		if (!$originalAddress instanceof AddressInterface) {
			return '';
		}

		// Options passed to the constructor or format() allow turning off
		// html rendering, customizing the wrapper element and its attributes.

		$address = new Address();
		$address = $address
			->withPostalCode($originalAddress->getZip())
			->withCountryCode($originalAddress->getCountryCode() ?? 'CZ')
			->withOrganization($originalAddress->getCompany())
			->withFamilyName($originalAddress->getFullName())
			->withAdministrativeArea($originalAddress->getRegionName())
			->withLocality($originalAddress->getCity())
			->withAddressLine2($originalAddress->getStreet() . ' ' . $originalAddress->getHouseNumber());
		$companyline = [];
		if ($registrationNo !== null && $registrationNo !== '') {
			$companyline[] = $this->translator->translate('messages.address.registrationNo') . ': ' . $registrationNo;
		}
		if ($vatId !== null && $vatId !== '') {
			$companyline[] = $this->translator->translate('messages.address.vatId') . ': ' . $vatId;
		}
		if (count($companyline) > 0) {
			$address = $address->withAddressLine1(join(PHP_EOL, $companyline));
		}

		return $this->formatter->format($address, ['html' => $html]);
	}
}
