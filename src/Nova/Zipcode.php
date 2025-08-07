<?php

namespace Marshmallow\Zipcode\Nova;

use Illuminate\Support\Str;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class Zipcode extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'zipcode';

    public function __construct($name, $zipcode_placeholder, $house_number_placeholder, $field_connections = [], ?callable $resolveCallback = null)
    {
        $this->name = $name;
        $this->zipcode_placeholder = $zipcode_placeholder;
        $this->house_number_placeholder = $house_number_placeholder;
        $this->resolveCallback = $resolveCallback;

        $this->default(null);

        $this->withMeta([
            'zipcode_placeholder' => $zipcode_placeholder,
            'house_number_placeholder' => $house_number_placeholder,
            'field_connections' => $field_connections,
            'zipcode_error' => __('The zipcode field should have a format of 1234AA'),
            'housenumber_error' => __('Please provide a house number'),
        ]);

        $this->street('street')
            ->city('city')
            ->zipcode('zipcode')
            ->housenumber('housenumber')
            ->province('province')
            ->country('country')
            ->latitude('latitude')
            ->longitude('longitude');
    }

    public function street($field)
    {
        return $this->withMeta(['street' => $field]);
    }

    public function city($field)
    {
        return $this->withMeta(['city' => $field]);
    }

    public function province($field)
    {
        return $this->withMeta(['province' => $field]);
    }

    public function zipcode($field)
    {
        return $this->withMeta(['zipcode' => $field]);
    }

    public function housenumber($field)
    {
        return $this->withMeta(['housenumber' => $field]);
    }

    public function country($field)
    {
        return $this->withMeta(['country' => $field]);
    }

    public function latitude($field)
    {
        return $this->withMeta(['latitude' => $field]);
    }

    public function longitude($field)
    {
        return $this->withMeta(['longitude' => $field]);
    }

    protected function resolveAttribute($resource, $attribute)
    {
        $zipcode = $this->meta()['zipcode'];
        $housenumber = $this->meta()['housenumber'];

        if (is_array($resource)) {
            return [
                (isset($resource[$zipcode])) ? $resource[$zipcode] : null,
                (isset($resource[$housenumber])) ? $resource[$housenumber] : null,
            ];
        }

        return [
            $resource->{$zipcode},
            $resource->{$housenumber},
        ];
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return void
     */
    protected function fillAttributeFromRequest(
        NovaRequest $request,
        $requestAttribute,
        $model,
        $attribute
    ) {
        $request->except($this->attribute);
    }
}
