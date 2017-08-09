# PackageFactory.Hyphenate

> Fusion Wrapper for [phpSyllable](https://github.com/vanderlee/phpSyllable)

## Installation

PackageFactory.Hyphenate is available via packagist. Just add `"packagefactory/hyphenate" : "~1.0.0"`
to the require-section of the composer.json or run `composer require packagefactory/hyphenate`.

## Usage

Just use the `PackageFactory.Hyphenate:Hyphenate` Fusion object as a processor on the fusion value that should be hyphenated.

```
someFusionValue = 'Grund­stücks­ver­kehrs­ge­neh­mi­gungs­zu­stän­dig­keits­über­tra­gungs­ver­ord­nung'
someFusionValue.@process.hyphenate = PackageFactory.Hyphenate:Hyphenate {
	locale = 'de'
}
```

**!!! Warning: !!!** This mechanism breaks HTML code. There's no support for hyphenating HTML Strings yet (PRs are welcome ;) ).

## Parameters

**locale** (string) : Reference to the language in which the given string will be hyphenated (Have a look at https://github.com/vanderlee/phpSyllable/tree/master/languages for a reference of available languages)

**threshold** (integer, default = 0) : Minimum amount characters a word needs to have, before it is being hyphenated.

## License

see [LICENSE file](LICENSE)
