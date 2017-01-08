Versionable Trait
=======================

### Setup

Add the `VersionableTrait` to the model you would like to track revisions for.

```php

class Content extends Model
{
    use VersionableTrait;
    protected $versionsEnabled = true;
    ....
}
```

### Usage

**Restore by version id**

```php
 Version::find($id)->getModel()->restoreVersion();
 ```

**List version for a "Versionable" resource**

```php
$content = Content::find($id);

foreach($content->versions as $content) {
    $content = $content->getModel();
    
}
```