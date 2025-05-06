# Filament Activity Log

<div class="filament-hidden">
    
[![Latest Version on Packagist](https://img.shields.io/packagist/v/swisnl/filament-activitylog.svg?style=flat-square)](https://packagist.org/packages/swisnl/filament-activitylog)
[![Software License](https://img.shields.io/packagist/l/swisnl/filament-activitylog.svg?style=flat-square)](LICENSE.md)
[![Buy us a tree](https://img.shields.io/badge/Treeware-%F0%9F%8C%B3-lightgreen.svg?style=flat-square)](https://plant.treeware.earth/swisnl/filament-activitylog)
[![Made by SWIS](https://img.shields.io/badge/%F0%9F%9A%80-made%20by%20SWIS-%230737A9.svg?style=flat-square)](https://www.swis.nl)

</div>


This package provides an interface to show activity log entries (from
[spatie/laravel-activitylog](https://github.com/spatie/laravel-activitylog)) in a Filament application. The interface
is exposed using actions. The actions show a modal with the activity log entries for the record. The package also
provides a way to add comments as activity log entries.

![Screenshot of Filament Demo Categories pages with Activity Log overlay modal](https://github.com/user-attachments/assets/b757818a-4d57-4baa-85b2-ab0cbc6a1144)


## Installation

You can install the package via composer:

```bash
composer require swisnl/filament-activitylog
```

If you didn't install the [spatie/laravel-activitylog](https://github.com/spatie/laravel-activitylog) package already,
this will also install that package. Follow the [installation instructions of spatie/laravel-activitylog](https://github.com/spatie/laravel-activitylog?tab=readme-ov-file#installation).

## Usage

The package provides two actions, one for tables, and one for pages. Add the action for the resources that have
activity. The actions show a modal with the activity log entries for the record and a form to add a comment.

### Tables

For tables add the `Swis\Filament\Activitylog\Tables\Actions\ActivitylogAction` to the actions in the resource table.

```php
<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Table;
use Swis\Filament\Activitylog\Tables\Actions\ActivitylogAction;

class MyResource extends Resource
{
    public static function table(Table $table): Table
    {
        return $table
            ...
            ->actions([
                ...
                ActivitylogAction::make(),
                ...
            ]);
    }
}
```

### Pages

For pages, use the `Swis\Filament\ActivityLog\Actions\ActivitylogAction`. The action can be added to the header actions
of the page. The example shows how to add the action to the `EditRecord` page, but the same logic applies to the
`ViewRecord` page, or other record pages.

```php
<?php

namespace App\Filament\Resources\MyResource\Pages;

use App\Filament\Resources\MyResource;
use Filament\Resources\Pages\EditRecord;
use Swis\Filament\ActivityLog\Actions\ActivitylogAction;

class EditContract extends EditRecord
{
    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...
            ActivitylogAction::make(),
        ];
    }
}
```

## Access control

By default, the actions use the `viewActivitylog` and `commentActivitylog` abilities on the model policy. If the policy
doesn't exist or the method is not defined in the policy, the action is shown to everyone.

```php
<?php

namespace App\Policies;

use App\Models\MyModel;
use App\Models\User;

class MyModelPolicy
{
    public function viewActivitylog(User $user, MyModel $myModel)
    {
        return $user->isAdmin();
    }

    public function commentActivitylog(User $user, MyModel $myModel)
    {
        return $user->isAdmin();
    }
}
```

Both actions also have methods to enable or disable the comment form. The comment form is enabled by default. You can
disable the comment form by calling the `disableComments` method on the action. If the comment form is disabled, the
policy is ignored for the comment form.

## Customizing the activity log entries

The package shows a row in the activity log for each activity log entry. Depending on the type of activity log entry,
the row is shown in a different way. The package provides a way to customize the display of the activity log entries.

By default, there is support for activity log entries with the following events: `created`, `updated`, `deleted` and
`commented`. This is done using view resolvers for entry content.

If you need to support other events, or if you need to override the default view resolvers, you can add your own view
resolvers. The view resolvers are called with the activity log entry. The view resolvers should return the name of the
view to render (or `null` if the view resolver can't handle the entry). You can add your own view resolvers by
calling the `FilamentActivitylog::registerEntryContentViewResolver` method. In most cases, you just want to map a 
specific event to a view. There is a helper method for this: 
`FilamentActivitylog::registerEntryContentEventViewResolver`.

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Swis\Filament\Activitylog\Facades\FilamentActivitylog;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        FilamentActivitylog::registerEntryContentEventViewResolver('myevent', 'activitylog.entries.myevent');
    }
}
```

In the example above, the package will render the `resources/views/activitylog/entries/myevent.blade.php` template for
activity log entries with the `myevent` event. The package will pass the activity log entry to the view as `$record`.
Look at the default views in the package to see how to render the activity log entries.

## Extending the attribute table

Activity entries can contain a table of attributes. The display of these attributes can be customized. The package
provides some ways to do this. For most projects, you will need to do some customization to fit your project.

### Labels

In the activity log data, we only have the key of the attribute. To show a human-readable label, we need to map the key
to a label. This is done using label providers. The default label provider simply runs the key through
`Str::headline`. You can add your own label providers by registering them in the
`FilamentActivitylog` facade.

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stringable;
use Swis\Filament\Activitylog\Facades\FilamentActivitylog;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        FilamentActivitylog::registerAttributeTableLabelProvider(function ($key, $recordClass) {
            $instance = new $recordClass;

            $translationKey = $instance->getMorphClass().'.fields.'.$key.'.label';
            $translation = __($translationKey);

            if ($translation === $translationKey) {
                return null;
            }

            return $translation;
        });
    }
}
```

In your custom logic, you need to return null if you can't provide a label. Return a string if you can provide a label.
The label provider is called with the key of the attribute and the class name of the record. All parameters are optional
in your closure, so omit the parameters you don't need. The parameters are matched by name, not by order. So feel free
to order them differently, but don't change the name.

The `FilamentActivitylog::registerAttributeTableLabelProvider` method accepts a second parameter, which is the
priority of the label provider. The default priority is 0. The higher the priority, the earlier the provider is called.
The built-in label providers has a negative priority, so they are called after custom providers (unless you explicitly
give them a lower priority).

There is one exception to this rule: the model specific overrides are also implemented as a label provider. This label
provider has a priority of 256. If you really need to run a label provider with more priority than the model specific
overrides, you can register a label provider with a priority higher than 256, but that should be an exceptional case.

#### Model specific overrides

You can also choose to provide the labels from the model. This can be useful if you don't have structured translations
for the labels, or if you want to provide the labels in a different way.

You can do this by implementing the `\Swis\Filament\Activitylog\AttributeTableContracts\LabelProvider` interface.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Swis\Filament\Activitylog\AttributeTable\Contracts\LabelProvider;

class MyModel extends Model implements LabelProvider
{
    public function getAttributeTableLabel(string $key, string $recordClass): ?string
    {
        return match ($key) {
            'myproperty' => 'My Property',
            default => null,
        };
    }
}
```

### Values

The values of the attributes are formatted using value formatters. You can add your own value formatters by registering
them in the `FilamentActivitylog` facade.

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Swis\Filament\Activitylog\AttributeTable\Builder;
use Swis\Filament\Activitylog\Facades\FilamentActivitylog;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        FilamentActivitylog::registerAttributeTableValueFormatter(function (Builder $builder, mixed $value, string $key, array $attributes, string $recordClass) {
            // Implement custom value formatting logic here.
        });
    }
}
```

In your custom logic, you need to return null if you can't format the value. Return a string or a `Stringable` object if
you can format the value. The attribute table supports HTML, so you can return an `Illuminate\Support\HtmlString` object
if you want to return HTML. All parameters are optional in your closure, so omit the parameters you don't need. The
parameters are matched by name, not by order. So feel free to order them differently, but don't change the name.

Because you get the builder as a parameter, you can use the values formatters from the builder recursively. If you do 
this, you convert `$value` to another object and call the formatter again with the new object, using
`$builder->formatValue($newValue, $key, $attributes, $recordClass)`. We do this in the default value formatters for
relations. The relation value formatter recognizes the relation from the foreign key and finds the appropriate related
model. This model is then passed to the builder again, so the builder can format the related model.

The `FilamentActivitylog::registerAttributeTableValueFormatter` method accepts a second parameter, which is the
priority of the formatter. The default priority is 0. The higher the priority, the earlier the formatter is called. All
built-in formatters have a negative priority, so they are called after custom formatters (unless you explicitly give
them a lower priority).

There is one exception to this rule: the model specific overrides are also implemented as a formatter. This formatter
has a priority of 256. If you really need to run a formatter with more priority than the model specific overrides, you
can register a formatter with a priority higher than 256, but that should be an exceptional case.

#### Model specific overrides

In rare cases you may need to format a specific attribute for a specific model differently than other values of the same
type.

In most cases you should prefer to use `FilamentActivitylog::registerAttributeTableValueFormatter` logic, and
match in the formatter on the type of the value, or casts of the model. If you really can't match based on this
information and need to match on the `$key` and `$recordClass` parameter, the model specific overrides can be a simpler
solution.

You can do this by implementing the `\Swis\Filament\Activitylog\AttributeTable\Contracts\ValuesFormatter` interface.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Swis\Filament\Activitylog\AttributeTable\Builder;
use Swis\Filament\Activitylog\AttributeTable\Contracts\ValueFormatter;

class MyModel extends Model implements ValueFormatter
{
    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Stringable|string|null
    {
        if ($key === 'myproperty') {
            return 'This is a custom formatted value';
        }
     
        return null;
    }
}
```

In the `formatAttributeTableValue` method you can call `$builder->formatValue(...)` if you need to convert the value
into another object and format this.

### Attribute skipping

In some cases, you may want to omit certain attributes in the attribute table. You can do this by implementing the
`\Swis\Filament\Activitylog\AttributeTable\Contracts\SkipsAttributes` interface on the model.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Swis\Filament\Activitylog\AttributeTable\Contracts\SkipsAttributes;

class MyModel extends Model implements SkipsAttributes
{
    public function skipAttributeTableAttributes() {
        return ['foo', 'bar'];
    }
}
```

For the relatively common use case of skipping the hidden attributes, you can use the `SkipsHiddenAttributes` trait
which is provided by the package.

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Swis\Filament\Activitylog\AttributeTable\Concerns\SkipsHiddenAttributes;
use Swis\Filament\Activitylog\AttributeTable\Contracts\SkipsAttributes;

class User extends Authenticatable implements SkipsAttributes
{
    use SkipsHiddenAttributes;
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email security@swis.nl instead of using the issue tracker.

## Credits

- [Rolf van de Krol](https://github.com/rolfvandekrol)
- [All Contributors](https://github.com/swisnl/filament-activitylog/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

This package is [Treeware](https://treeware.earth). If you use it in production, then we ask that you
[**buy the world a tree**](https://plant.treeware.earth/swisnl/filament-activitylog) to thank us for our work. By
contributing to the Treeware forest you’ll be creating employment for local families and restoring wildlife habitats.

## SWIS ❤️ Open Source

[SWIS](https://www.swis.nl) is a web agency from Leiden, the Netherlands. We love working with open source software.
