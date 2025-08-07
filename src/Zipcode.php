<?php

namespace Marshmallow\Zipcode;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class Zipcode
{
    protected $street = 'street';

    protected $city = 'city';

    protected $province = 'province';

    protected $country = 'country';

    protected $latitude = 'latitude';

    protected $longitude = 'longitude';

    protected $flexible_id_prefix = null;

    public function get(string $zip_code, ?string $house_number = null)
    {
        $response_array = $this->callGeoDataApi($zip_code, $house_number);
        $this->validateApiResponse($response_array);

        $address_information = $this->getAddressInformation($response_array);

        return response()->json([
            $this->street => $address_information['straatnaam'],
            $this->city => $address_information['woonplaatsnaam'],
            $this->province => $address_information['provincienaam'],
            $this->country => 'NL',
            $this->latitude => $this->getLatitudeFromResponse($address_information),
            $this->longitude => $this->getLongitudeFromResponse($address_information),
        ]);
    }

    protected function parsePointLocation($address_information, $key)
    {
        $location = Str::of($address_information['centroide_ll'])
            ->replace('POINT(', '')
            ->replace(')', '')
            ->explode(' ');

        return $location[$key];
    }

    protected function getLatitudeFromResponse(array $address_information)
    {
        return $this->parsePointLocation($address_information, 0);
    }

    protected function getLongitudeFromResponse(array $address_information)
    {
        return $this->parsePointLocation($address_information, 1);
    }

    protected function getAddressInformation(array $response_array)
    {
        return $response_array['response']['docs'][0];
    }

    protected function validateApiResponse(array $response_array)
    {
        if (!isset($response_array['response']['docs'][0])) {
            throw new Exception(__('No address was found.'), 1);
        }
    }

    protected function callGeoDataApi(string $zip_code, ?string $house_number = null)
    {
        $zip_code = str_replace(' ', '', $zip_code);
        $url = "https://api.pdok.nl/bzk/locatieserver/search/v3_1/free?fq=postcode:{$zip_code}";
        if ($house_number) {
            $url .= "&fq=huisnummer:{$house_number}";
        }
        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception(__('The National Geo Register is down at the moment.'), 2);
    }

    public function flexibleIdPrefix(?string $flexible_id_prefix = null): self
    {
        $this->flexible_id_prefix = ($flexible_id_prefix !== 'null') ? $flexible_id_prefix : null;
        return $this;
    }

    public function street($field)
    {
        $this->street = $this->buildFieldName($field);
        return $this;
    }

    public function city($field)
    {
        $this->city = $this->buildFieldName($field);
        return $this;
    }

    public function province($field)
    {
        $this->province = $this->buildFieldName($field);
        return $this;
    }

    public function country($field)
    {
        $this->country = $this->buildFieldName($field);
        return $this;
    }

    public function latitude($field)
    {
        $this->latitude = $this->buildFieldName($field);
        return $this;
    }

    public function longitude($field)
    {
        $this->longitude = $this->buildFieldName($field);
        return $this;
    }

    protected function buildFieldName(string $field_name): string
    {
        if ($this->flexible_id_prefix !== null) {
            return "{$this->flexible_id_prefix}__{$field_name}";
        }
        return $field_name;
    }
}
